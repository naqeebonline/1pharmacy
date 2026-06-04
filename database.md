# Database Schema

Generated: 2025-09-06 21:03:01
Database: hospital (mysql)

## Tables
- [student_details](##student_details)
- [students](##students)

---

### student_details

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  |  |
| student_id | int | NO |  |  |
| mobile | varchar(255) | YES |  |  |
| address | varchar(255) | YES |  |  |
| contact | varchar(255) | YES |  |  |
| phone | varchar(255) | YES |  |  |

*Indexes*

- INDEX forign_key on (student_id)

---

### students

*Columns*

| Column | Type | Null | Default | Extra |
|---|---|---:|---|---|
| id | int | NO |  | auto_increment |
| name | varchar(255) | YES |  |  |
| father_name | varchar(255) | YES |  |  |
| email | varchar(255) | YES |  |  |

*Primary Key:* id

*Indexes*

- UNIQUE PRIMARY on (id)

---

> *Copilot Hint:* Use this schema to generate Laravel Eloquent models with relationships.
> - belongsTo for each FK.
> - hasOne if child FK is unique, else hasMany.
> - belongsToMany for pairs connected via pivot tables.
