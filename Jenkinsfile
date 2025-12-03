pipeline {
    agent any

    environment {
        DB_HOST = "db-galera"
        DB_USER = "root"
        DB_PASS = "secret"
        DB_NAME = "MainData"
    }

    stages {

        stage('1. PHP Lint') {
            steps {
                sh '''
                    echo "Проверяем синтаксис PHP..."
                    find app -type f -name "*.php" -print0 | xargs -0 -n1 php -l
                '''
            }
        }

        stage('2. Проверка таблиц в базе данных') {
            steps {
                sh '''
                    echo "Проверяем наличие нужных таблиц в БД..."

                    REQUIRED_TABLES="users catalog Shop orders order_details"

                    for TBL in $REQUIRED_TABLES; do
                        echo -n "Проверяем таблицу $TBL ... "
                        if mysql -h${DB_HOST} -u${DB_USER} -p${DB_PASS} -e "USE ${DB_NAME}; SHOW TABLES LIKE '$TBL';" | grep -q "$TBL"; then
                            echo "OK"
                        else
                            echo "ОШИБКА: таблица $TBL отсутствует!"
                            exit 1
                        fi
                    done
                '''
            }
        }

        stage('3. Проверка количества строк (минимальных данных)') {
            steps {
                sh '''
                    echo "Проверяем, есть ли сущности в таблицах..."

                    mysql -h${DB_HOST} -u${DB_USER} -p${DB_PASS} -e "
                        SELECT 'users', COUNT(*) FROM ${DB_NAME}.users;
                        SELECT 'Shop', COUNT(*) FROM ${DB_NAME}.Shop;
                        SELECT 'catalog', COUNT(*) FROM ${DB_NAME}.catalog;
                    "
                '''
            }
        }
    }

    post {
        success {
            echo "Все проверки прошли успешно ✔"
        }
        failure {
            echo "Пайплайн упал ❌ Проверь логи"
        }
    }
}