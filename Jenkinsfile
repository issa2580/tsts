pipeline {
    environment {
        SONARQUBE_URL = 'http://localhost:9000'
        SONARQUBE_TOKEN = credentials('sonar-token')
        SONARQUBE_PROJECT = 'ligne-rouge'
        SLACK_CHANNEL = '#groupe4'
        SLACK_TOKEN= credentials('slack-token')
        DOCKER_REGISTRY = 'registry.hub.docker.com'
        DOCKER_CREDENTIALS_ID = 'docker-credentiel'
        DOCKER_COMPOSE_FILE = 'docker-compose.yml' 
        KUBECONFIG = "/home/rootkit/.kube/config"
        TERRA_DIR  = "/home/rootkit/tst/ligne-rouge/terraform"
        ANSIBLE_DIR = "/home/rootkit/tst/ligne-rouge/ansible"
    }
    agent any
    stages {
        stage('Build Docker images') {
            steps {
                script {
                    sh 'docker-compose up --build -d'
                }
            }
        }
        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('SonarQube') {
                    sh '/opt/sonar-scanner-6.0.0.4432-linux/bin/sonar-scanner -Dsonar.projectKey=$SONARQUBE_PROJECT -Dsonar.sources=. -Dsonar.host.url=$SONARQUBE_URL -Dsonar.login=$SONARQUBE_TOKEN'
                }
            }
        }
        stage('Notify Quality Gate with Slack') {
            when {
                expression {
                    currentBuild.result == 'SUCCESS'
                }
            }
            steps {
                script {
                    def color = currentBuild.result == 'SUCCESS' ? 'ok' : 'danger'
                    def message = "Build ${currentBuild.result}: ${env.JOB_NAME} - ${env.BUILD_NUMBER} \n More details at: ${env.BUILD_URL}"

                    slackSend(
                        color: color,
                        message: message,
                        channel: env.SLACK_CHANNEL,
                        tokenCredentialId: env.SLACK_TOKEN
                    )
                }
            }
        }
        stage('Pushing Images to Docker Registry') {
            steps {
                script {
                    def dockerRegistry = "https://registry.hub.docker.com/"
                    def dockerUsername = "martinez42"
                    def dockerPassword = "Passer@4221"
                    def images = ["web", "sonarqube", "postgres", "db"]
                    sh "docker login $dockerRegistry -u $dockerUsername -p $dockerPassword"
                    images.each { image ->
                        def localImage = "ligne-rouge-${image}:latest"
                        def remoteImage = "${dockerUsername}/ligne-rouge-${image}:latest"
                        sh "docker tag ${localImage} ${remoteImage}"
                        sh "sudo docker push ${remoteImage}"
                    }
                }
            }
        }
        stage("Provision Kubernetes Cluster with Terraform") {
            steps {
                script {
                    sh """
                    cd ${TERRA_DIR}
                    terraform init
                    terraform plan
                    terraform apply --auto-approve
                    """
                }
            }
        }
        stage('Install Python dependencies and Deploy with Ansible') {
            steps {
                script {
                    sh """
                    sudo apt-get install -y python3-venv
                    cd ${ANSIBLE_DIR}
                    sudo python3 -m venv venv
                    . venv/bin/activate
                    sudo pip install kubernetes
                    ansible-playbook ${ANSIBLE_DIR}/playbook.yml
                    """
                }
            }
        }
    }
    post {
        success {
            slackSend channel: 'groupe4', message: 'Success to deploy'
        }
        failure {
            slackSend channel: 'groupe4', message: 'Failed to deploy'
        }
    }
}