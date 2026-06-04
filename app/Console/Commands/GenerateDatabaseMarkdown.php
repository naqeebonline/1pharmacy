<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generate a strong database.md from the live database schema.
 *
 * Features
 * - Supports MySQL/MariaDB and PostgreSQL.
 * - Lists tables, columns (type, nullability, default), PKs, FKs, indexes (unique/non-unique).
 * - Infers relationships:
 *      belongsTo  -> for each FK
 *      hasOne     -> if FK on child is UNIQUE
 *      hasMany    -> default inverse of FK when not unique
 *      belongsToMany -> for pivot tables (2 FKs, no other strong columns)
 * - Nicely formatted Markdown with a TOC.
 *
 * Usage:
 *   php artisan schema:markdown  # writes database.md in project root
 *   php artisan schema:markdown --path=docs/schema.md
 */
class GenerateDatabaseMarkdown extends Command
{
    //php artisan schema:markdown --tables=finance_heads,finance_transactions
    //or
    //php artisan schema:markdown
    /*protected $signature = 'schema:markdown {--path=database.md : Output path relative to base_path()}';*/
    protected $signature = 'schema:markdown
    {--path=database.md : Output path relative to base_path()}
    {--tables= : Comma-separated list of tables to include}';
    protected $description = 'Generate a comprehensive database.md with PK/FK, indexes and inferred Eloquent relationships';

protected string $driver;
protected string $database;

    public function handle(): int
    {
        $this->driver = DB::connection()->getDriverName();
        $this->database = DB::getDatabaseName();

        if (!in_array($this->driver, ['mysql', 'pgsql'])) {
            $this->error("Only MySQL/MariaDB and PostgreSQL are supported. Detected driver: {$this->driver}");
            return self::FAILURE;
        }

        $tables = $this->getTables();
        if (empty($tables)) {
            $this->warn('No tables found.');
            return self::SUCCESS;
        }
        if ($this->option('tables')) {
            $selected = array_map('trim', explode(',', $this->option('tables')));
            $tables = array_intersect($tables, $selected);
        }

        if (empty($tables)) {
            $this->warn('No matching tables found.');
            return self::SUCCESS;
        }

        // Collect schema details
        $schema = [];
        foreach ($tables as $t) {
            $schema[$t] = [
                'columns' => $this->getColumns($t),
                'primary' => $this->getPrimaryKeys($t),
                'foreign' => $this->getForeignKeys($t),
                'indexes' => $this->getIndexes($t),
            ];
        }

        // Relationship inference
        $relationships = $this->inferRelationships($schema);

        // Build markdown
        $markdown = $this->buildMarkdown($schema, $relationships);

        $outPath = base_path($this->option('path'));
        @mkdir(dirname($outPath), 0777, true);
        file_put_contents($outPath, $markdown);

        $this->info("✅ Schema markdown generated: {$outPath}");
        return self::SUCCESS;
    }

    /** ----------------------------
     *  SCHEMA QUERIES
     * ----------------------------*/

    protected function getTables(): array
    {
        if ($this->driver === 'mysql') {
            $rows = DB::select("
    SELECT TABLE_NAME as name 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = ? 
      AND TABLE_TYPE = 'BASE TABLE' 
    ORDER BY TABLE_NAME
", [$this->database]);
        } else { // pgsql
            $rows = DB::select("SELECT tablename as name FROM pg_catalog.pg_tables WHERE schemaname = 'public' ORDER BY tablename");
        }
        return array_map(fn($r) => $r->name, $rows);
    }

    protected function getColumns(string $table): array
    {
        if ($this->driver === 'mysql') {
            $rows = DB::select('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION', [$this->database, $table]);
            return collect($rows)->map(function ($r) {
                return [
                    'name' => $r->COLUMN_NAME,
                    'type' => $r->COLUMN_TYPE,
                    'nullable' => $r->IS_NULLABLE === 'YES',
                    'default' => $r->COLUMN_DEFAULT,
                    'extra' => $r->EXTRA,
                ];
            })->keyBy('name')->toArray();
        }
        // pgsql
        $rows = DB::select(
            "SELECT a.attname AS COLUMN_NAME,
                    pg_catalog.format_type(a.atttypid, a.atttypmod) AS COLUMN_TYPE,
                    NOT a.attnotnull AS IS_NULLABLE,
                    pg_get_expr(ad.adbin, ad.adrelid) AS COLUMN_DEFAULT
             FROM pg_attribute a
             JOIN pg_class c ON a.attrelid = c.oid
             JOIN pg_namespace n ON c.relnamespace = n.oid
             LEFT JOIN pg_attrdef ad ON a.attrelid = ad.adrelid AND a.attnum = ad.adnum
             WHERE c.relname = ? AND n.nspname = 'public' AND a.attnum > 0 AND NOT a.attisdropped
             ORDER BY a.attnum",
            [$table]
        );
        return collect($rows)->map(function ($r) {
            return [
                'name' => $r->column_name,
                'type' => $r->column_type,
                'nullable' => (bool)$r->is_nullable,
                'default' => $r->column_default,
                'extra' => null,
            ];
        })->keyBy('name')->toArray();
    }

    protected function getPrimaryKeys(string $table): array
    {
        if ($this->driver === 'mysql') {
            $rows = DB::select('SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = "PRIMARY" ORDER BY ORDINAL_POSITION', [$this->database, $table]);
            return array_map(fn($r) => $r->COLUMN_NAME, $rows);
        }
        $rows = DB::select(
            "SELECT a.attname AS COLUMN_NAME
             FROM pg_index i
             JOIN pg_class c ON c.oid = i.indrelid
             JOIN pg_namespace n ON n.oid = c.relnamespace
             JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum = ANY(i.indkey)
             WHERE i.indisprimary = true AND n.nspname = 'public' AND c.relname = ?",
            [$table]
        );
        return array_map(fn($r) => $r->column_name, $rows);
    }

    protected function getForeignKeys(string $table): array
    {
        if ($this->driver === 'mysql') {
            $rows = DB::select(
                'SELECT k.CONSTRAINT_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME
                 FROM information_schema.KEY_COLUMN_USAGE k
                 WHERE k.TABLE_SCHEMA = ? AND k.TABLE_NAME = ? AND k.REFERENCED_TABLE_NAME IS NOT NULL
                 ORDER BY k.POSITION_IN_UNIQUE_CONSTRAINT',
                [$this->database, $table]
            );
            return collect($rows)->map(function ($r) {
                return [
                    'name' => $r->CONSTRAINT_NAME,
                    'column' => $r->COLUMN_NAME,
                    'ref_table' => $r->REFERENCED_TABLE_NAME,
                    'ref_column' => $r->REFERENCED_COLUMN_NAME,
                ];
            })->toArray();
        }
        // pgsql
        $rows = DB::select(
            "SELECT
                tc.constraint_name AS CONSTRAINT_NAME,
                kcu.column_name AS COLUMN_NAME,
                ccu.table_name AS REFERENCED_TABLE_NAME,
                ccu.column_name AS REFERENCED_COLUMN_NAME
            FROM information_schema.table_constraints AS tc
            JOIN information_schema.key_column_usage AS kcu
              ON tc.constraint_name = kcu.constraint_name AND tc.table_schema = kcu.table_schema
            JOIN information_schema.constraint_column_usage AS ccu
              ON ccu.constraint_name = tc.constraint_name AND ccu.table_schema = tc.table_schema
            WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = 'public' AND tc.table_name = ?
            ORDER BY kcu.ordinal_position",
            [$table]
        );
        return collect($rows)->map(function ($r) {
            return [
                'name' => $r->constraint_name,
                'column' => $r->column_name,
                'ref_table' => $r->referenced_table_name,
                'ref_column' => $r->referenced_column_name,
            ];
        })->toArray();
    }

    protected function getIndexes(string $table): array
    {
        if ($this->driver === 'mysql') {
            $rows = DB::select('SHOW INDEX FROM '.$table.'');
            // group by index name
            $byName = collect($rows)->groupBy('Key_name');
            $indexes = [];
            foreach ($byName as $name => $group) {
                $indexes[] = [
                    'name' => $name,
                    'unique' => ($group->first()->Non_unique == 0),
                    'columns' => $group->pluck('Column_name')->values()->all(),
                ];
            }
            return $indexes;
        }
        // pgsql
        $rows = DB::select(
            "SELECT i.relname as index_name, ix.indisunique as unique, array_agg(a.attname ORDER BY a.attnum) as columns
             FROM pg_class t
             JOIN pg_namespace n ON n.oid = t.relnamespace AND n.nspname = 'public'
             JOIN pg_index ix ON t.oid = ix.indrelid
             JOIN pg_class i ON i.oid = ix.indexrelid
             JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(ix.indkey)
             WHERE t.relkind = 'r' AND t.relname = ?
             GROUP BY i.relname, ix.indisunique
             ORDER BY i.relname",
            [$table]
        );
        return collect($rows)->map(fn($r) => [
        'name' => $r->index_name,
        'unique' => (bool)$r->unique,
        'columns' => array_values((array) $r->columns),
    ])->toArray();
    }

    /** ----------------------------
     *  RELATIONSHIP INFERENCE
     * ----------------------------*/

    protected function inferRelationships(array $schema): array
    {
        $rels = [];

        // Precompute unique sets to detect hasOne
        $uniqueMap = [];
        foreach ($schema as $table => $meta) {
            $uniqueMap[$table] = collect($meta['indexes'] ?? [])
                ->filter(fn($i) => $i['unique'])
                ->map(fn($i) => $i['columns'])
                ->values()->all();
        }

        // Identify pivot tables (belongsToMany)
        $pivotTables = $this->detectPivotTables($schema);

        foreach ($schema as $table => $meta) {
            $rels[$table] = [
                'belongsTo' => [],
                'hasOne' => [],
                'hasMany' => [],
                'belongsToMany' => [],
            ];
        }

        // belongsTo + hasOne/hasMany
        foreach ($schema as $table => $meta) {
            foreach ($meta['foreign'] as $fk) {
                $child = $table;
                $parent = $fk['ref_table'];
                $fkCol = $fk['column'];

                // belongsTo on child
                $rels[$child]['belongsTo'][] = [
                    'model' => $this->modelName($parent),
                    'table' => $parent,
                    'fk'    => $fkCol,
                    'ref'   => $fk['ref_column'],
                ];

                // hasOne or hasMany on parent
                $isUnique = $this->isUniqueCombination($uniqueMap[$child] ?? [], [$fkCol]);
                $type = $isUnique ? 'hasOne' : 'hasMany';
                $rels[$parent][$type][] = [
                    'model' => $this->modelName($child),
                    'table' => $child,
                    'fk'    => $fkCol,
                    'ref'   => $fk['ref_column'],
                ];
            }
        }

        // belongsToMany via pivots
        foreach ($pivotTables as $pivot => $pairs) {
            [$a, $b] = $pairs; // [ [table=>..., column=>...], [table=>..., column=>...] ]
            $rels[$a['table']]['belongsToMany'][] = [
                'model' => $this->modelName($b['table']),
                'table' => $b['table'],
                'pivot' => $pivot,
                'fk'    => $a['column'],
                'otherKey' => $b['column'],
            ];
            $rels[$b['table']]['belongsToMany'][] = [
                'model' => $this->modelName($a['table']),
                'table' => $a['table'],
                'pivot' => $pivot,
                'fk'    => $b['column'],
                'otherKey' => $a['column'],
            ];
        }

        return $rels;
    }

    protected function isUniqueCombination(array $uniqueSets, array $columns): bool
    {
        foreach ($uniqueSets as $set) {
            sort($set); $cols = $columns; sort($cols);
            if ($set === $cols) return true;
        }
        return false;
    }

    protected function detectPivotTables(array $schema): array
    {
        $pivots = [];
        foreach ($schema as $table => $meta) {
            $fks = $meta['foreign'];
            // Pivot heuristic: exactly two FKs, and (optionally) a simple primary key or composite unique of those two
            if (count($fks) === 2) {
                // Ensure no other strong columns except timestamps/id allowed
                $columns = collect($meta['columns'])->keys()->all();
                $nonTrivial = collect($columns)->reject(function ($c) use ($fks) {
                    if (in_array($c, ['id', 'created_at', 'updated_at', 'deleted_at'])) return true;
                    foreach ($fks as $fk) if ($fk['column'] === $c) return true;
                    return false;
                })->values();

                if ($nonTrivial->count() === 0) {
                    $pivots[$table] = [
                        ['table' => $fks[0]['ref_table'], 'column' => $fks[0]['column']],
                        ['table' => $fks[1]['ref_table'], 'column' => $fks[1]['column']],
                    ];
                }
            }
        }
        return $pivots;
    }

    protected function modelName(string $table): string
    {
        return Str::studly(Str::singular($table));
    }

    /** ----------------------------
     *  MARKDOWN BUILDER
     * ----------------------------*/

    protected function buildMarkdown(array $schema, array $rels): string
    {
        $now = now()->toDateTimeString();
        $md = "# Database Schema\n\nGenerated: {$now}\nDatabase: {$this->database} ({$this->driver})\n\n";

        // TOC
        $md .= "## Tables\n";
        foreach (array_keys($schema) as $t) {
            $md .= "- [".$t."](##".strtolower($t).")\n";
        }
        $md .= "\n---\n\n";

        foreach ($schema as $table => $meta) {
            $md .= "### {$table}\n\n";

            // Columns table
            $md .= "*Columns*\n\n";
            $md .= "| Column | Type | Null | Default | Extra |\n|---|---|---:|---|---|\n";
            foreach ($meta['columns'] as $col) {
                $md .= sprintf(
                    "| %s | %s | %s | %s | %s |\n",
                    $col['name'],
                    $this->escMd($col['type'] ?? ''),
                    $col['nullable'] ? 'YES' : 'NO',
                    $this->escMd($this->fmtDefault($col['default'])),
                    $this->escMd($col['extra'] ?? '')
                );
            }
            $md .= "\n";

            // Keys
            if (!empty($meta['primary'])) {
                $md .= "*Primary Key:* ".implode(', ', $meta['primary'])."\n\n";
            }

            // Foreign keys
            if (!empty($meta['foreign'])) {
                $md .= "*Foreign Keys*\n\n";
                foreach ($meta['foreign'] as $fk) {
                    $md .= "- {$fk['column']} → {$fk['ref_table']}.{$fk['ref_column']}\n";
                }
                $md .= "\n";
            }

            // Indexes
            if (!empty($meta['indexes'])) {
                $md .= "*Indexes*\n\n";
                foreach ($meta['indexes'] as $idx) {
                    $u = $idx['unique'] ? 'UNIQUE' : 'INDEX';
                    $md .= "- {$u} {$idx['name']} on (".implode(', ', $idx['columns']).")\n";
                }
                $md .= "\n";
            }

            // Relationships for this table
            $r = $rels[$table] ?? null;
            if ($r && (count($r['belongsTo']) || count($r['hasOne']) || count($r['hasMany']) || count($r['belongsToMany']))) {
                $md .= "*Inferred Eloquent Relationships*\n\n";
                foreach (['belongsTo','hasOne','hasMany','belongsToMany'] as $type) {
                    if (!empty($r[$type])) {
                        $md .= "- {$type}:\n";
                        foreach ($r[$type] as $x) {
                            if ($type === 'belongsToMany') {
                                $md .= "  - {$x['model']} (via {$x['pivot']}, fk: {$x['fk']}, otherKey: {$x['otherKey']})\n";
                            } else {
                                $md .= "  - {$x['model']} (fk: {$x['fk']} → {$x['ref']})\n";
                            }
                        }
                    }
                }
                $md .= "\n";
            }

            $md .= "---\n\n";
        }

        // Copilot hint block
        $md .= "> *Copilot Hint:* Use this schema to generate Laravel Eloquent models with relationships.\n> - belongsTo for each FK.\n> - hasOne if child FK is unique, else hasMany.\n> - belongsToMany for pairs connected via pivot tables.\n";

        return $md;
    }

    protected function escMd($v): string
    {
        $s = (string)($v ?? '');
        return str_replace('|', '\\|', $s);
    }

    protected function fmtDefault($v): string
    {
        if ($v === null) return '';
        $s = (string)$v;
        // Clean PG defaults like "nextval('seq'::regclass)"
        return preg_replace('/::[a-z_]+/i', '', $s);
    }
}