pipeline {
    agent any

    environment {
        STACK = "mystack"
        COMPOSE_FILE = "docker-compose.yaml"
    }

    stages {
        stage('1. Проверка Docker Swarm') {
            steps {
                script {
                    sh """
                        if ! docker info | grep -q 'Swarm: active'; then
                            docker swarm init || true
                        fi
                    """
                }
            }
        }

        stage('2. Очистка старого стека') {
            steps {
                script {
                    sh """
                        docker stack rm ${STACK} || true
                        sleep 10
                    """
                }
            }
        }

        stage('3. Развертывание стека') {
            steps {
                script {
                    sh """
                        docker stack deploy --with-registry-auth -c ${COMPOSE_FILE} ${STACK}
                    """
                }
            }
        }

        stage('4. Проверка сервисов') {
            steps {
                sh 'docker service ls'
                sh 'docker service ps ${STACK}_web-server || true'
                sh 'docker service ps ${STACK}_db || true'
            }
        }
    }
}
