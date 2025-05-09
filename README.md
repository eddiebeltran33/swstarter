# SWStarter - A Star Wars API - Lawnstarter Take Home Test

## Requirements

- Docker and Docker compose installed (tested on Docker Desktop for Mac version v4.40.0)
- Ports 3306, 80 and 6379 open and not used by other services

## Installation

1. Clone the repository
    ```bash
    git clone https://github.com/eddiebeltran33/swstarter.git &&
    cd swstarter
    ```
2. Create the `.env` file

    ```bash
    cp .env.example .env
    ```

3. Install dependencies backend dependencies

    ```bash
    docker compose run --rm composer install --ignore-platform-reqs --no-interaction --prefer-dist --no-scripts
    ```

4. Install dependencies frontend dependencies
    ```bash
    docker compose run --rm node npm install
    ```
5. Boot up Laravel Sail
    ```bash
    ./vendor/bin/sail up
    #you can also do ./vendor/bin/sail up -d to run in detached mode
    ```
6. Migrate the database (Do this in a new terminal)
    ```bash
    ./vendor/bin/sail artisan migrate
    ```
7. Run Laravel Horizon (Do this in a new terminal)
    ```bash
    ./vendor/bin/sail artisan horizon
    ```
8. Run the Schedule worker to process the metrics (Do this in a new terminal)
    ```bash
    ./vendor/bin/sail artisan schedule:work
    ```
9. Run the tests to check if everything is working
    ```bash
    ./vendor/bin/sail test
    ```
10. You should be able to access the app at [http://localhost](http://localhost)

## Endpoints

- [x] /api/v1/people (for searching)
- [x] /api/v1/people/{id}
- [x] /api/v1/movies (for searching)
- [x] /api/v1/movies/{id}

## Analytics

- [x] Log individual request (metrics: time, status code, endpoint)
- [x] Top five people resources in a day
- [x] Top five people search terms by in a day
- [x] Top five movie resources visited by in a day
- [x] Top five movie search terms by in a day
- [x] Total errors in a day
- [x] Average request time by in a day

## Architecture

- [ ] Use a cache layer to avoid hitting the SWAPI too often
- [x] Use a queue to process the "traces" (metrics) in the background
- [ ] Backoff strategy for the SWAPI in case of rate limiting
- [ ] Use the same rate limiting policy as the SWAPI, once we have a cache layer use a more generous policy
- [x] Create a SWAPI client to handle the requests to the SWAPI
- [ ] Use jaeger and a opentelemetry setup to instrument (more realisticaly) the requests

## Code Quality and Testing

- [x] Setup ESLint
- [x] Setup Laravel Pint
- [ ] Setup Github Actions to run tests and linting
- [ ] Setup PHPStan
- [ ] Setup PHPMD
- [-] Test 404 cases and invalid data
- [ ] Install husky and prevent commit if there are linting errors
- [ ] Inertia endpoint tests (Laravel dusk)
- [ ] Mock the SWAPI client to avoid hitting the SWAPI
- [ ] Setup load testing with k6
- [ ] Setup EditorConfig for VSCode

## UI

- [x] Pagination for people
- [x] Detail page for people
- [x] Detail page for movies
- [x] Clean the search results on search type change
- [x] Filter by name or tile either movies or people in the Index
- [ ] Create a button component
- [ ] Override tailwind colors and typography with Swstarter Zeplin mockup values
- [ ] Migrate to Typescript
- [ ] A flash message component to display error messages

## Bugs and misconfigurations

- [ ] total_pages is not correctly set when listing all people (always displays 1)
- [ ] Laravel telescope and laravel horizon auth gates are not properly configured
- [ ] Resource id must be sanitized before being stored in the database
- [ ] The search term in both people and movie endpoints should probably be normalized for easier grouping in
      aggregation process

## Poetic Licenses (stuff that wouldn't cut it in a real project)

- The decision of waiting 1 minute for the metrics to be aggregated is just a finger in the air (it's impossible to be
  sure that this time will be enough to process all the requests that arrive to the system in the previous 5 mins).
- I'm modifying already commited migrations.
- I would not use this setup for real instrumentation / APM. I'm learning about the open telemetry
  ecosystem, but If I had to take a decision right now, I would go with an APM like Elastic APM or even Sentry.
- request_metrics table has created_at, started_at and ended_at columns. I've tried many strategies to fetch records
  by intervals considering the time of the write vs the time of the request measured by the server, but this requires
  a deeper dive taking into consideration that a there is potential of leaving records oustide the window interval
  aggregation process just because they are still on the queue.
