// ── Drop-in replacement for loadFeedback() in admin-dashboard.js ─────────────
// Replace the existing loadFeedback function with this one.

async function loadFeedback() {
    const result = await fetchAPI('get_feedback.php');
    const list   = document.getElementById('feedback-list');

    if (!result.success || !result.data.length) {
        list.innerHTML = '<p class="empty-state">No feedback yet.</p>';
        return;
    }

    const typeEmoji = {
        general:    '💬',
        suggestion: '💡',
        bug:        '🐛',
        complaint:  '⚠️'
    };

    const modeLabel = {
        single_player: 'Single Player',
        timed_quiz:    'Timed Quiz',
        ranked_quiz:   'Ranked Quiz',
        memory_match:  'Memory Match',
        endless_quiz:  'Endless Quiz'
    };

    list.innerHTML = result.data.map(f => {
        const stars   = f.rating ? '★'.repeat(f.rating) + '☆'.repeat(5 - f.rating) : 'No rating';
        const emoji   = typeEmoji[f.feedback_type] || '💬';
        const modeLbl = f.quiz_mode ? (modeLabel[f.quiz_mode] || f.quiz_mode) : '—';
        const date    = new Date(f.created_at).toLocaleString();

        return `
        <div class="feedback-item ${f.status}">

            <div class="feedback-meta-top">
                <span class="feedback-type-badge">${emoji} ${f.feedback_type || 'general'}</span>
                <span class="feedback-status-badge ${f.status}">${f.status.toUpperCase()}</span>
            </div>

            <div class="feedback-labels">
                <span>👤 <strong>${f.user_name || 'Anonymous'}</strong></span>
                <span>📝 ${f.quiz_title    || 'No quiz linked'}</span>
                <span>🗂️ ${f.quiz_category || '—'}</span>
                <span>🎮 ${modeLbl}</span>
                <span>⭐ ${stars}</span>
            </div>

            <p class="feedback-body">"${f.feedback_text}"</p>

            <div class="feedback-footer">
                <span class="feedback-date">🕒 ${date}</span>
                <div class="feedback-actions">
                    ${f.status === 'pending'
                        ? `<button class="btn-action btn-edit"
                             onclick="resolveFeedback(${f.id})">✅ Resolve</button>`
                        : ''}
                    <button class="btn-action btn-delete"
                            onclick="deleteFeedbackItem(${f.id})">🗑️ Delete</button>
                </div>
            </div>
        </div>`;
    }).join('');
}

// Resolve feedback
async function resolveFeedback(id) {
    const result = await fetchAPI('resolve_feedback.php', 'POST', { id });
    if (result.success) {
        showToast('Feedback resolved', 'success');
        loadFeedback();
    } else {
        showToast(result.message, 'error');
    }
}

// Delete feedback
async function deleteFeedbackItem(id) {
    if (!confirm('Delete this feedback?')) return;
    const result = await fetchAPI('delete_feedback.php', 'POST', { id });
    if (result.success) {
        showToast('Feedback deleted', 'success');
        loadFeedback();
        loadDashboardStats();
    } else {
        showToast(result.message, 'error');
    }
}
