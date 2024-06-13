pipeline {
    environment {
        SONARQUBE_URL = 'http://localhost:9000'
        SONARQUBE_TOKEN = credentials('token-sonarqube')
        SONARQUBE_PROJECT = 'tst'
        webDockerImageName = "martinez42/file-rouge-web"
        dbDockerImageName = "martinez42/file-rouge-db"
        webDockerImage = ""
        dbDockerImage = ""
        registryCredential = 'docker-credentiel'
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
                    sh """
                    ${SONAR_SCANNER_HOME}/bin/sonar-scanner \
                    -Dsonar.projectKey=${SONARQUBE_PROJECT} \
                    -Dsonar.sources=. \
                    -Dsonar.host.url=${SONARQUBE_URL} \
                    -Dsonar.login=${SONARQUBE_TOKEN}
                    """
                }
            }
        }
        // stage('Build Web Docker image') {
        //     steps {
        //         script {
        //             webDockerImage = docker.build webDockerImageName, "-f apache.Dockerfile ."
        //         }
        //     }
        // }
        // stage('Build DB Docker image') {
        //     steps {
        //         script {
        //             dbDockerImage = docker.build dbDockerImageName, "-f mysql.Dockerfile ."
        //         }
        //     }
        // }
        // stage('Pushing Images to Docker Registry') {
        //     steps {
        //         script {
        //             docker.withRegistry('https://registry.hub.docker.com', registryCredential) {
        //                 webDockerImage.push('latest')
        //                 dbDockerImage.push('latest')
        //             }
        //         }
        //     }
        // }
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
                    pip install kubernetes ansible
                    ansible-playbook ${ANSIBLE_DIR}/playbook.yml
                    """
                }
            }
        }
    }
    // post {
    //     success {
    //         slackSend channel: 'groupe4', message: 'Success to deploy'
    //     }
    //     failure {
    //         slackSend channel: 'groupe4', message: 'Failed to deploy'
    //     }
    // }
}