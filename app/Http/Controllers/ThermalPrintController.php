<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ThermalPrintController extends Controller
{
    public function printReceipt()
    {
        try {
            $connector = new WindowsPrintConnector("XP-80C-Thermal");
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("My Shop\n");
            $printer->text("123 Main Street\n");
            $printer->text("-------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Invoice #: 001\n");
            $printer->text("Date: " . now()->format('Y-m-d') . "\n");
            $printer->text("Customer: John Doe\n");
            $printer->text("-------------------------------\n");

            // Header with spacing
            $printer->text(
                str_pad("Item", 18) .
                str_pad("Qty", 12, ' ', STR_PAD_LEFT) .
                str_pad("Price", 8, ' ', STR_PAD_LEFT) . "\n"
            );
            $printer->text("-------------------------------\n");

            // Sample items
            $items = [
                ['name' => 'Apple', 'qty' => 2, 'price' => 200],
                ['name' => 'Orange', 'qty' => 1, 'price' => 150],
                ['name' => 'Watermelon Big Size', 'qty' => 1, 'price' => 300],
            ];

            foreach ($items as $item) {
                $name = substr($item['name'], 0, 18); // limit name to 18 chars
                $qty = str_pad($item['qty'], 12, ' ', STR_PAD_LEFT);
                $price = str_pad(number_format($item['price'], 0), 8, ' ', STR_PAD_LEFT);
                $printer->text(str_pad($name, 18) . $qty . $price . "\n");
            }

            $printer->text("-------------------------------\n");
            $printer->text(str_pad("Total:", 24) . str_pad("650", 8, ' ', STR_PAD_LEFT) . "\n");

            $printer->text("\nThank you!\n\n\n");

            $printer->cut();
            $printer->close();

            return response("Printed successfully");

        } catch (\Exception $e) {
            return response("Print failed: " . $e->getMessage(), 500);
        }
    }

    public function printReceipt2()
    {
        try {
            $connector = new WindowsPrintConnector("XP-80C-Thermal"); // Use your printer name
            $printer = new Printer($connector);

            // Your HTML table (example)
            $html = '
            <table>
                <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
                <tr><td>Apple</td><td>2</td><td>200</td></tr>
                <tr><td>Orange</td><td>1</td><td>150</td></tr>
            </table>
        ';

            // Load and parse HTML
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Suppress HTML5 warnings
            $dom->loadHTML($html);
            libxml_clear_errors();

            $rows = $dom->getElementsByTagName('tr');

            $printer->text("Invoice\n");
            $printer->text(str_repeat("-", 32) . "\n");
            foreach ($rows as $row) {
                $cols = $row->getElementsByTagName('td');
                if ($cols->length === 0) {
                    // Handle <th>
                    $cols = $row->getElementsByTagName('th');
                }

                $line = '';
                foreach ($cols as $col) {
                    $line .= str_pad($col->nodeValue, 10);
                }

                $printer->text($line . "\n");
            }

            $printer->text(str_repeat("-", 32) . "\n");
            $printer->cut();
            $printer->close();

            return "Printed successfully.";
        } catch (\Exception $e) {
            return "Print failed: " . $e->getMessage();
        }
    }
}
