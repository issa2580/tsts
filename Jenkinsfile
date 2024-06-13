pipeline {
    environment {
        SONARQUBE_URL = 'http://localhost:9000'
        SONARQUBE_TOKEN = credentials('sonar-token')
        SONARQUBE_PROJECT = 'ligne-rouge'
        // webDockerImageName = "martinez42/file-rouge-web"
        // dbDockerImageName = "martinez42/file-rouge-db"
        // webDockerImage = ""
        // dbDockerImage = ""
        // registryCredential = 'docker-credentiel'
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
        // stage('Quality Gate') {
        //     steps {
        //         script {
        //             timeout(time: 1, unit: 'HOURS') {
        //                 waitForQualityGate abortPipeline: true
        //             }
        //         }
        //     }
        // }
        stage('Pushing Images to Docker Registry') {
            steps {
                script {
                    def dockerRegistry = "https://registry.hub.docker.com/"
                    def dockerUsername = "martinez42"
                    def dockerPassword = "Passer@4221"
                    sh "docker login $dockerRegistry -u $dockerUsername -p $dockerPassword"
                    sh "docker push ligne-rouge_master-web:latest"
                    sh "docker push ligne-rouge_master-sonarqube:latest"
                    sh "docker push ligne-rouge_master-postgres:latest"
                    sh "docker push ligne-rouge_master-db:latest"
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