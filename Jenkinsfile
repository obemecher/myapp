pipeline {
    agent any

    environment {
        STACK         = "delivery-service"
        COMPOSE_FILE  = "docker-compose.yaml"
        APP_DIR       = "app"
    }

    stages {
        stage('1. Проверка PHP-синтаксиса') {
            steps {
                script {
                    echo "🔎 Проверка синтаксиса всех .php файлов в папке ${APP_DIR}/..."

                    // Ищем все .php файлы
                    def files = sh(
                        script: "find ${APP_DIR} -type f -name '*.php' | sort",
                        returnStdout: true
                    ).trim()

                    if (!files) {
                        echo "⚠️ Нет PHP-файлов для проверки."
                        return
                    }

                    def fileList = files.split('\n')
                    echo "Найдено файлов: ${fileList.size()}"

                    // Проверяем каждый файл
                    for (file in fileList) {
                        file = file.trim()
                        if (file) {
                            echo "Проверяю: $file"
                            sh "php -l '$file'"
                        }
                    }

                    echo "✅ Все PHP-файлы прошли синтаксическую проверку."
                }
            }
        }

        stage('2. Поиск опасных функций') {
            steps {
                script {
                    echo "🔍 Поиск потенциально опасных функций в коде..."

                    def result = sh(
                        script: "grep -r --include='*.php' -E 'eval|exec|shell_exec|system|passthru|popen|assert' ${APP_DIR}/ || true",
                        returnStdout: true
                    ).trim()

                    if (result) {
                        echo "⚠️ Найдены подозрительные вызовы:\n${result}"
                        // Опционально: раскомментируйте, чтобы остановить сборку
                        // error("Обнаружены опасные функции в коде!")
                    } else {
                        echo "✅ Опасные функции не найдены."
                    }
                }
            }
        }

        stage('3. Проверка Docker Swarm') {
            steps {
                script {
                    sh '''
                        if ! docker info 2>/dev/null | grep -q "Swarm: active"; then
                            echo "Инициализация Docker Swarm..."
                            docker swarm init
                        fi
                    '''
                }
            }
        }

        stage('4. Очистка старого стека') {
            steps {
                script {
                    sh """
                        docker stack rm ${STACK} || true
                        sleep 10
                    """
                }
            }
        }

        stage('5. Развертывание стека') {
            steps {
                script {
                    sh """
                        docker stack deploy --with-registry-auth -c ${COMPOSE_FILE} ${STACK}
                    """
                }
            }
        }

        stage('6. Проверка запущенных сервисов') {
            steps {
                sh 'docker service ls'
                sh "docker service ps ${STACK}_web-server || true"
                sh "docker service ps ${STACK}_db-galera || true"
            }
        }
    }

    post {
        success {
            echo "✅ Пайплайн успешно завершён. Приложение развернуто."
        }
        failure {
            echo "❌ Пайплайн завершился с ошибкой."
        }
    }
}