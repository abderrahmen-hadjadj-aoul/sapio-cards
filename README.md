# sapio-cards

Flash card web app to learn efficiently

# Install

`git clone git@github.com:abderrahmen-hadjadj-aoul/sapio-cards.git`
`cd sapio-cards`
`composer install`
`cd front`
`yarn install`

# Build

`cd front`
`yarn build`

# Test

make tests

# Run locally

docker-compose up
symfony server:start

# Create test user

symfony console app:create-test-user
email: unit.test@unit.test
password: test
apikey: TEST-API-KEY

# Enjoy

Go to
https://localhost:8000/

# Deploy

# Develop

`cd front`
`yarn serve`

Go to
http://localhost:8080/

