
# README (CODE EXAMPLE)
This solution is based on Symfony Flex plugin with Symfony 5.

Requirements:
- composer
- web server (e.g. Apache)
- MySQL server

How to Run:
1. Install composer libraries
    >composer install
2. Generate __dev__ environment. It will create file _.env.local.php_
    >composer dump-env dev
3. Make sure that file _.env.local.php_ has correct mapping to your MySQL server and database
4. Create database (optional)
    >bin/console doctrine:database:create
5. Apply migration (generate schema)
    >bin/console doctrine:migrations:migrate
6. Fill fixtures
    >bin/console doctrine:fixtures:load
7. set up web server. Note that public directory is __web__ folder


## More notes
I decided to use it because it allows to extend server functionality "step by step".
I wanted to show you Doctrine DBAL usage but then I came to thought that I need full Doctrine to generate migrations.
By the way.. MySQL Schema and SQL as well as DQL are tested manually. The only thing I'd change now is date interval selection but it's not very meaningful.

You asked me to implement 2 different datasources. actually Doctrine allows that and even switch between them but here MySQL is only used. I planned to create Excel/TSV parser but this task took more time than it was expected, so I decided to commit it at the moment and ask if you need more deeper implementation.

I used TWIG template manager but it's not mandatory all could be rendered with simple PHP files.

- Class _App\Stats\TaxIncome_ is a service which implements Fasade pattern as any usual service. If you read it you will see all queries: SQL/DQL. Some queries are placed into EntityRepository.

- _App/Migrations/Version20191125150149_ this is migration file. it is autogenerated with command
    >bin/console make:migration
You can see Database structure with this file

- _App/DataFixtures/AppFixtures_ this is fixture's file.

- upfront/service/config/routes.yaml this file has routes configuration. It is possible to configure routes with annotations but yaml configuration is more flexible


