# SWStarter - A Star Wars API - Lawnstarter Take Home Test

## Requirements

- Docker and Docker compose installed (tested on Docker Desktop for Mac version v4.40.0) )

## Installation

1. Clone the repository
    ```bash
    git clone ...
    cd swstarter
    ```
2. Install dependencies
    ```bash
    make install
    ```

## Endpoints

- [x] /api/v1/people (for searching)
- [x] /api/v1/people/{id}
- [x] /api/v1/movies (for searching)
- [x] /api/v1/movies/{id}

## Analytics

- [ ] Log individual request (metrics: time, status code, endpoint)
- [ ] Top five people queried by 5 minute interval
- [ ] Top five people searches by 5 minute interval
- [ ] Top five movies queried by 5 minute interval
- [ ] Top five movies searches by 5 minute interval
- [ ] Average request time by 5 minute interval

## Architecture

- [ ] Use a cache layer to avoid hitting the SWAPI too often
- [ ] Use a queue to process the "traces" (metrics) in the background
- [ ] Create a scheduled task to aggreate the metrics every N minutes
- [ ] Backoff strategy for the SWAPI in case of rate limiting
- [ ] Use the same rate limiting policy as the SWAPI, once we have a cache layer use a more generous policy
- [ ] Create a SWAPI client to handle the requests to the SWAPI and swap it at test time to mock the responses

## Code Quality and Testing

- [ ] Setup ESLint
- [ ] Setup Laravel Pint
- [ ] Setup Github Actions to run tests and linting
- [ ] Setup PHPStan
- [ ] Setup PHPMD

## UI

- [ ] Pagination for people
- [x] Detail page for people
- [x] Detail page for movies
- [ ] Clean the search results on search type change
- [x] Filter by name or tile either movies or people in the Index
- [ ] Create a button component
- [ ] Override tailwind colors and typography with Swstarter colors
- [ ] Migrate to Typescript
