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
   git checkout local
   ```
3. Build Docker Image
   ```sh
   docker build -t beastlybot:local .
   ```
4. Run Docker Container
   ```sh
    docker run -p 8080:8000 beastlybot:local
   ```
5. Hit the site
   ```sh
    localhost:8080
   ```



