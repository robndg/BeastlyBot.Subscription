<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

- Docker

### Installation

1. Clone the repo
   ```sh
   git clone git@github.com:JoeAshworth/BeastlyBot.git
   ```
2. Checkout Local (Local Development)
   ```sh
   git checkout Master
   ```
3. Build Docker Image
   ```sh
   docker build -t beastlybot:master .
   ```
4. Run Docker Container
   ```sh
    docker run -p 8080:8000 beastlybot:master
   ```
5. Watch the deployment
   ```sh
    https://github.com/JoeAshworth/BeastlyBot/actions
   ```
6. Hit the site
   ```sh
   https://beastly.app
   ```
