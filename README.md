# バックアップ

```bash
mysql -u [db_user] -p [db_name] > /etc/mysql/sql/dump.sql
```

or

```bash
# こっちのが早い
mysqldump -u [db_user] -p[password] [db_name] > /etc/mysql/sql/dump.sql

```

# リストア

```bash
mysql -u [db_user] -p [db_name] < /etc/mysql/sql/dump.sql
```
