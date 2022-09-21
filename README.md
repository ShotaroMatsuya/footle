[![Known Vulnerabilities](https://snyk.io/test/github/ShotaroMatsuya/footle/badge.svg)](https://snyk.io/test/github/ShotaroMatsuya/footle)

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

# Problem

1. mysqldump で PROCESS 権限を要求される

```sql
mysql > GRANT PROCESS ON *.* TO 'your-user';
```

確認

```sql
mysql > SHOW GRANTS FOR 'your-user';
```

## job

```bash
./watchdog.sh 300 php job1.php <url>
```

## login

public repository への認証トークン更新

```bash
aws ecr-public get-login-password --region us-east-1 | docker login --username AWS --password-stdin public.ecr.aws
```

ECR private repository の認証トークン更新

```bash
aws ecr get-login-password --region ap-northeast-1 | docker login --username AWS --password-stdin 528163014577.dkr.ecr.ap-northeast-1.amazonaws.com
```

## app init

```bash
copilot app init footle
```

## env init & deploy

```bash
copilot env init --name prod
copilot env deploy --name prod
```

## secret init

```bash
copilot secret init --cli-input-yaml env/mysql.yaml
```

## svc init & deploy

```bash
copilot svc init --name mysql
copilot svc deploy --name mysql --env prod
```

```bash
copilot svc init --name php
copilot svc deploy --name php --env prod
```

## job init & deploy

```bash
copilot job init --name crawling
copilot job deploy --name crawling --env prod
```

## pipeline init & deploy

```bash
copilot pipeline init --name

```

## clean up resources

```bash
copilot app delete
```
