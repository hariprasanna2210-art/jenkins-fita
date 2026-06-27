pipeline {
    agent any

    environment {
        GIT_URL = 'https://github.com/hariprasanna2210-art/jenkins-fita.git'
        GIT_BRANCH = 'main'
    }

    stages {

        stage('Checkout') {
            steps {
                git branch: "${GIT_BRANCH}",
                    url: "${GIT_URL}"
            }
        }

        stage('Debug') {
            steps {
                sh '''
                    echo "Current Directory:"
                    pwd

                    echo "Workspace Files:"
                    ls -la

                    echo "Root Directories:"
                    ls -la /

                    echo "Checking /var/www/html"
                    ls -la /var/www/html || true
                '''
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                    mkdir -p /var/www/html
                    rm -rf /var/www/html/*
                    cp -r ./* /var/www/html/
                '''
            }
        }
    }

    post {
        success {
            echo 'Deployment Successful!'
        }

        failure {
            echo 'Deployment Failed!'
        }
    }
}