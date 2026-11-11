# Performance, Reporting, and Continuous Improvement

## Current controls

- Major assessment, certificate, hospital, and officer lists are bounded to 50
  database records per server-rendered page.
- Filter query parameters are retained when navigating pages.
- Frequently traversed foreign keys use `select_related`, and Phase 7 added
  indexes for status, ownership, hospital, county, and officer filters.
- Medical and health dashboard status counts use one filtered aggregate query
  instead of one query per status.
- Every response includes a `Server-Timing` application duration and structured
  completion logs include duration, status, method, view name, and request ID.
  Raw paths are deliberately excluded because verification paths contain tokens.

## Initial service objectives

Measure at the HTTPS edge and review monthly:

- Availability: 99.5% successful readiness during service hours.
- Interactive HTML: p95 below 1.5 seconds and p99 below 3 seconds.
- Public verification: p95 below 750 ms, excluding rate-limited requests.
- Error rate: fewer than 1% unexpected HTTP 5xx responses.
- Queue freshness: 99% of notifications created within one minute of a decision.

These are initial targets, not measured claims. Establish a 30-day baseline,
segment by view name and status, and revise targets with service owners.

## Reporting privacy

Operational reports should use aggregates by authorized county, hospital,
status, and disability category. Suppress or combine small cells where disclosure
risk exists. Do not export names, identity numbers, certificate tokens, clinical
notes, or reviewer identifiers into analytics systems without a separately
approved purpose, access model, retention period, and audit trail.

## Backlog process

Track slow views, query counts, support issues, accessibility findings, and user
feedback in one maintained backlog. Prioritize measured user impact. Move email,
PDF, or report generation to background workers only after timing data shows it
is materially affecting request latency.
