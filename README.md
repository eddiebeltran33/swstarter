# TO-DOS

## Endpoints

- [x] /api/v1/people (for searching)
- [x] /api/v1/people/{id}
- [x] /api/v1/movies (for searching)
- [x] /api/v1/movies/{id}

## Analitics

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
