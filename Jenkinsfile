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
        DOCKER_REGISTRY = 'https://registry.hub.docker.com'
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
        stage('Login and Push Docker Images') {
            steps {
                script {
                    docker.withRegistry("https://${DOCKER_REGISTRY}", DOCKER_CREDENTIALS_ID) {
                        def services = ['web', 'db', 'postgres', 'sonarqube']
                        
                        for (service in services) {
                            def imageName = "${DOCKER_REGISTRY}/${service}"
                            sh "docker-compose -f ${DOCKER_COMPOSE_FILE} images | grep ${service} | awk '{print \$3}' | xargs -I {} docker tag {} ${imageName}:latest"
                            sh "docker push ${imageName}:latest"
                        }
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