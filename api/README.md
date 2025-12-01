# API

REST API that powers the vocab trainer.

## Endpoints

- GET `/api/vocab` — list all
- GET `/api/vocab?id=1` — get one
- POST `/api/vocab` — create `{ lang_a, lang_b, meta? }`
- PUT `/api/vocab?id=1` — update `{ lang_a, lang_b, meta? }`
- DELETE `/api/vocab?id=1` — delete

Auth (optional): `Authorization: Bearer <API_KEY>`

Errors: `{ "error": "message" }`

## Env

Copy `.env.example` to `.env` and edit with relevant information

Then create the schema (see `sql/schema.sql`).

## API quick reference

- GET `/api/vocab` — list all
- GET `/api/vocab?id=1` — get one
- POST `/api/vocab` — create `{ lang_a, lang_b, meta? }`
- PUT `/api/vocab?id=1` — update `{ lang_a, lang_b, meta? }`
- DELETE `/api/vocab?id=1` — delete

Auth (optional): `Authorization: Bearer <API_KEY>`

Errors look like: `{ "error": "message" }`

## Meta (optional, validated)

- `word_type`: one of `noun|verb|adjective|adverb|pronoun|preposition|conjunction|interjection|article|phrase`
- `conjugation`: object of string → string or string[]
- `sessions`: `{ [sessionId]: { right: int>=0, wrong: int>=0 } }`

Example payload

```json
{
  "lang_a": "be",
  "lang_b": "essere",
  "meta": {
    "word_type": "verb",
    "conjugation": {
      "present": ["sono", "sei", "è", "siamo", "siete", "sono"]
    },
    "sessions": {
      "2025-11-01": {"right": 3, "wrong": 1}
    }
  }
}
```
