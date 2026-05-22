# TechStack

A WordPress theme for aggregating European tech events. Built as a portfolio project to practice WordPress theme development, custom post types, ACF, and real data integration.

## What it does

Pulls together conferences, hackathons, meetups, workshops, summits and webinars happening across Europe into one filterable index. Data was sourced from confs.tech, devpost.com, lablab.ai, and mlh.io — no fake entries.

Currently lists 68 events across 20+ cities.

## Pages

- **Events** — filterable archive with sidebar filters for type, city, topic, date range, price and format
- **Hackathons** — dedicated view pre-filtered to hackathons with team and prize context
- **CFP Open** — events still accepting speaker proposals, sorted by deadline urgency
- **Calendar** — monthly grid view with event type color coding and day popovers
- **Submit Event** — public submission form, drafts go to admin review queue

## Stack

- WordPress 7.0
- Advanced Custom Fields (ACF) for all event metadata
- PHP 8.2
- Custom child theme — no page builder
- LocalWP for local development
- Vanilla JS for all filtering and interactivity

## Custom post type

`tech_event` with 30+ ACF fields covering dates, location, format, topics, speakers, certificates, scholarships, CFP info, audience level, and programming languages.

## Data

All events are real. Sources: confs.tech, devpost.com, lablab.ai, mlh.io, eventbrite.com. Events outside Europe were excluded during import. No generated or placeholder content.

## Local setup

1. Install LocalWP
2. Create a new site called `techstack`
3. Copy this theme folder to `wp-content/themes/`
4. Install and activate Advanced Custom Fields plugin
5. Activate the theme
6. Go to Settings > Permalinks and save

You will need to import event data separately — the database is not included in this repo.

## Author

Cemre Mete — [cemremete.dev](https://cemremete.dev)
