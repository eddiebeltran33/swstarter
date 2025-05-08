# SWStarter - A Star Wars API - Lawnstarter Take Home Test

## Requirements

- Docker and Docker compose installed (tested on Docker Desktop for Mac version v4.40.0)
- Ports 3306, 80 and 6379 open and not used by other services

## Installation

1. Clone the repository
    ```bash
    git clone https://github.com/eddiebeltran33/swstarter.git
    ```
2. Install dependencies (not working yet)
    ```bash
    ./setup.sh
    ```

## Endpoints

- [x] /api/v1/people (for searching)
- [x] /api/v1/people/{id}
- [x] /api/v1/movies (for searching)
- [x] /api/v1/movies/{id}

## Analytics

- [x] Log individual request (metrics: time, status code, endpoint)
- [ ] Top five people resources visited by 5 minute interval
- [ ] Top five people search terms by 5 minute interval
- [ ] Top five movie resources visited by 5 minute interval
- [ ] Top five movie search terms by 5 minute interval
- [ ] Average request time by 5 minute interval

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
- [ ] Test 404 cases and invalid data
- [ ] Install husky and prevent commit if there are linting errors
- [ ] Inertia endpoint tests (Laravel dusk)
- [ ] Mock the SWAPI client to avoid hitting the SWAPI

## UI

- [x] Pagination for people
- [x] Detail page for people
- [x] Detail page for movies
- [x] Clean the search results on search type change
- [x] Filter by name or tile either movies or people in the Index
- [ ] Create a button component
- [ ] Override tailwind colors and typography with Swstarter colors
- [ ] Migrate to Typescript

## Bugs and misconfigurations

- [ ] total_pages is not correctly set when listing all people (always displays 1)
- [ ] Laravel telescope and laravel horizon auth gates are not properly configured

## Poetic Licenses

- I'm modifying already commited migrations. I wouldn't do this in a real project.
