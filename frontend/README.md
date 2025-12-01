# Frontend

HTML widgets & JS client for the API intended to be used as iframes

## Quick start (JS client)

```javascript
// init (common local URL)
const api = new VocabAPI('http://localhost/index.php/api/vocab');

// CRUD
await api.create({ lang_a: 'hello', lang_b: 'ciao', meta: { word_type: 'interjection' }});
const all = await api.getAll();
const one = await api.getById(1);
await api.update(1, { lang_a: 'hello', lang_b: 'buongiorno', meta: { word_type: 'phrase' } });
await api.delete(1);

// Extras
await api.updateSessionStats(1, '2025-11-27', true);
const results = await api.search('hello');
const random = await api.getRandom();
const verbs = await api.getByWordType('verb');
const weak = await api.getByPerformance('2025-11-27', 'asc');
```

If auth is enabled, pass a key:

```js
const api = new VocabAPI('http://localhost/index.php/api/vocab', 'your-api-key');
```

## HTML widgets (iframe)

Edit `API_BASE_URL` (and `API_KEY` if needed) at the top of the script block inside the file.

- `vocab-quiz.html`: Type the translation, instant feedback.
- `vocab-flashcards.html`: Flip cards, track right/wrong per day.
- `vocab-list.html`: Table view with search + filter.

```html
<iframe src="vocab-quiz.html" width="100%" height="600" frameborder="0"></iframe>
<iframe src="vocab-flashcards.html" width="100%" height="700" frameborder="0"></iframe>
<iframe src="vocab-list.html" width="100%" height="600" frameborder="0"></iframe>
```
