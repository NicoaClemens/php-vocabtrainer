/**
 * Vocabulary Trainer API Helper
 * JavaScript client library for the PHP Vocabulary Trainer API
 */

class VocabAPI {
    /**
     * @param {string} baseUrl - Base URL of the API (e.g., 'http://localhost/api/vocab')
     * @param {string|null} apiKey - Optional API key for authentication
     */
    constructor(baseUrl, apiKey = null) {
        this.baseUrl = baseUrl.replace(/\/$/, ''); // Remove trailing slash
        this.apiKey = apiKey;
    }

    /**
     * Get headers for API requests
     * @private
     */
    _getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
        };
        if (this.apiKey) {
            headers['Authorization'] = `Bearer ${this.apiKey}`;
        }
        return headers;
    }

    /**
     * Handle API response
     * @private
     */
    async _handleResponse(response) {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || `HTTP ${response.status}: ${response.statusText}`);
        }
        return data;
    }

    /**
     * Get all vocabulary entries
     * @returns {Promise<Array>}
     */
    async getAll() {
        const response = await fetch(this.baseUrl, {
            method: 'GET',
            headers: this._getHeaders(),
        });
        return this._handleResponse(response);
    }

    /**
     * Get a single vocabulary entry by ID
     * @param {number} id - Vocabulary ID
     * @returns {Promise<Object>}
     */
    async getById(id) {
        const response = await fetch(`${this.baseUrl}?id=${id}`, {
            method: 'GET',
            headers: this._getHeaders(),
        });
        return this._handleResponse(response);
    }

    /**
     * Create a new vocabulary entry
     * @param {Object} vocab - Vocabulary data
     * @param {string} vocab.lang_a - First language word/phrase
     * @param {string} vocab.lang_b - Second language word/phrase
     * @param {Object|null} vocab.meta - Optional metadata
     * @returns {Promise<Object>}
     */
    async create(vocab) {
        const response = await fetch(this.baseUrl, {
            method: 'POST',
            headers: this._getHeaders(),
            body: JSON.stringify(vocab),
        });
        return this._handleResponse(response);
    }

    /**
     * Update an existing vocabulary entry
     * @param {number} id - Vocabulary ID
     * @param {Object} vocab - Updated vocabulary data
     * @returns {Promise<Object>}
     */
    async update(id, vocab) {
        const response = await fetch(`${this.baseUrl}?id=${id}`, {
            method: 'PUT',
            headers: this._getHeaders(),
            body: JSON.stringify(vocab),
        });
        return this._handleResponse(response);
    }

    /**
     * Delete a vocabulary entry
     * @param {number} id - Vocabulary ID
     * @returns {Promise<Object>}
     */
    async delete(id) {
        const response = await fetch(`${this.baseUrl}?id=${id}`, {
            method: 'DELETE',
            headers: this._getHeaders(),
        });
        return this._handleResponse(response);
    }

    /**
     * Update session statistics for a vocabulary entry
     * @param {number} id - Vocabulary ID
     * @param {string} sessionId - Session identifier (e.g., '2025-11-27')
     * @param {boolean} correct - Whether the answer was correct
     * @returns {Promise<Object>}
     */
    async updateSessionStats(id, sessionId, correct) {
        const vocab = await this.getById(id);
        
        if (!vocab.meta) {
            vocab.meta = {};
        }
        if (!vocab.meta.sessions) {
            vocab.meta.sessions = {};
        }
        if (!vocab.meta.sessions[sessionId]) {
            vocab.meta.sessions[sessionId] = { right: 0, wrong: 0 };
        }

        if (correct) {
            vocab.meta.sessions[sessionId].right++;
        } else {
            vocab.meta.sessions[sessionId].wrong++;
        }

        return this.update(id, {
            lang_a: vocab.lang_a,
            lang_b: vocab.lang_b,
            meta: vocab.meta,
        });
    }

    /**
     * Get vocabulary entries filtered by word type
     * @param {string} wordType - Word type to filter by
     * @returns {Promise<Array>}
     */
    async getByWordType(wordType) {
        const all = await this.getAll();
        return all.filter(v => v.meta && v.meta.word_type === wordType);
    }

    /**
     * Search vocabulary entries (client-side search)
     * @param {string} query - Search query
     * @returns {Promise<Array>}
     */
    async search(query) {
        const all = await this.getAll();
        const lowerQuery = query.toLowerCase();
        return all.filter(v => 
            v.lang_a.toLowerCase().includes(lowerQuery) ||
            v.lang_b.toLowerCase().includes(lowerQuery)
        );
    }

    /**
     * Get random vocabulary entry
     * @returns {Promise<Object>}
     */
    async getRandom() {
        const all = await this.getAll();
        if (all.length === 0) {
            throw new Error('No vocabulary entries available');
        }
        const randomIndex = Math.floor(Math.random() * all.length);
        return all[randomIndex];
    }

    /**
     * Get vocabulary entries sorted by session performance
     * @param {string} sessionId - Session identifier
     * @param {string} order - 'asc' for worst first, 'desc' for best first
     * @returns {Promise<Array>}
     */
    async getByPerformance(sessionId, order = 'asc') {
        const all = await this.getAll();
        const withStats = all.filter(v => 
            v.meta && v.meta.sessions && v.meta.sessions[sessionId]
        );

        return withStats.sort((a, b) => {
            const statsA = a.meta.sessions[sessionId];
            const statsB = b.meta.sessions[sessionId];
            const ratioA = statsA.right / (statsA.right + statsA.wrong) || 0;
            const ratioB = statsB.right / (statsB.right + statsB.wrong) || 0;
            return order === 'asc' ? ratioA - ratioB : ratioB - ratioA;
        });
    }
}

// node
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VocabAPI;
}
