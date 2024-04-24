russian
english
german
spanish
italian
mongolian
chinese
kazakh
another

'russian','english','german','spanish','italian','mongolian','chinese','kazakh','another'

```sql
ALTER TABLE `bree7e_cris_publications` CHANGE COLUMN language language ENUM('russian','english','german','spanish','italian','mongolian','chinese','kazakh') NOT NULL DEFAULT 'russian' AFTER `edition`;
```

```php
DB::statement("ALTER TABLE `bree7e_cris_publications` CHANGE COLUMN language language ENUM('russian','english','german','spanish','italian','mongolian','chinese','kazakh','another') NOT NULL DEFAULT 'russian' AFTER `edition`;");
```

