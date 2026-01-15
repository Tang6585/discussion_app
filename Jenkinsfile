pipeline {
    agent any

    stages {
        stage('Checkout Source') {
            steps {
                git branch: 'main', url: 'https://github.com/Tang6585/discussion_app.git'
            }
        }

        stage('Syntax Check') {
            steps {
                sh 'php -l dashboard.php'
            }
        }

        stage('Tests') {
            steps {
                sh 'echo Simulating tests...'
            }
        }

        stage('Deploy') {
            steps {
                sh 'echo Deploy stage would run if build succeeds'
            }
        }
    }

    post {
        success {
            echo '🎉 SUCCESS: dashboard.php passed syntax check!'
        }
        failure {
            echo '❌ FAILURE: Fix dashboard.php and push again.'
        }
    }
}












































