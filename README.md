# バックアップ

```bash
mysql -u [db_user] -p [db_name] > /etc/mysql/sql/dump.sql
```

# リストア

```bash
mysql -u [db_user] -p [db_name] < /etc/mysql/sql/dump.sql
```
