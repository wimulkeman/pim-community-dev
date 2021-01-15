# Events API Ubiquitous Language

- **events api**: Public API that define the requests sent to external application in reaction to pim event in the PIM

- **connection**: Configuration of an entry point into the PIM API (+ user permissions)
- **event subscription**: Configuration of a Connection with a URL of destination for the Events API (+ secret & activation/deactivation of the event subscription)

- **event type**: Today, we have 6 event types: product updated, product created, product removed, product model created, product model updated, and product model  removed

- **pim event**: Event created in reaction to lifecycle event (create, update, remove) on one PIM domain entity (product, product model, ...)
- **pim event bulk**: Collection of pim events

- **(api/to send) event**: Event sent by the Events API to event subscription URL (event information and data with permissions applied)
- **request**: Request sent to the event subscription URL, it contains a collection of events (infos & data)
