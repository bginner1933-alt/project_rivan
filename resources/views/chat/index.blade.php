@extends('layouts.app')

@section('content')

<style>
/* =============================================
   CHAT WRAPPER
   ============================================= */
.chat-box {
    max-height: 460px;
    overflow-y: auto;
    background: #efeae2;
    background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
    scroll-behavior: smooth;
}

/* =============================================
   BUBBLE
   ============================================= */
.msg-container {
    max-width: 75%;
    position: relative;
}

.bubble {
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 0.92rem;
    word-break: break-word;
    position: relative;
    line-height: 1.45;
}

.bubble.me {
    background: linear-gradient(135deg, #25d366, #128C7E);
    color: #fff;
    border-top-right-radius: 4px;
    box-shadow: 0 1px 4px rgba(18,140,126,0.25);
}

.bubble.you {
    background: #fff;
    color: #111;
    border-top-left-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.time {
    font-size: 10.5px;
    margin-top: 4px;
    opacity: 0.65;
    text-align: right;
    white-space: nowrap;
}

.bubble.me .time { color: #e0f5ef; }

/* =============================================
   HOVER ACTION MENU
   ============================================= */
.msg-wrap {
    position: relative;
    display: flex;
    align-items: center;
    gap: 4px;
}

.msg-actions {
    opacity: 0;
    transition: opacity 0.15s ease;
    flex-shrink: 0;
}

.msg-wrap:hover .msg-actions { opacity: 1; }

.msg-actions .btn {
    padding: 2px 5px;
    color: #6c757d;
    line-height: 1;
}

/* =============================================
   IMAGE PREVIEW (INPUT)
   ============================================= */
.preview-img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #dee2e6;
}

#previewContainer {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}

/* =============================================
   CONTACT LIST
   ============================================= */
.contact-item.active-contact {
    background: #128C7E !important;
    color: #fff !important;
}

.contact-item.active-contact small {
    color: rgba(255,255,255,0.7) !important;
}

/* =============================================
   SELECTION MODE
   ============================================= */
.select-mode {
    display: none;
    align-items: center;
    flex-shrink: 0;
}

.selection-mode-active .select-mode { display: flex !important; }

.chat-checkbox {
    appearance: none;
    -webkit-appearance: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #adb5bd;
    background: #fff;
    cursor: pointer;
    position: relative;
    flex-shrink: 0;
    transition: background 0.15s, border-color 0.15s;
}

.chat-checkbox:checked {
    background: #25d366;
    border-color: #25d366;
}

.chat-checkbox:checked::after {
    content: '';
    position: absolute;
    top: 3px; left: 6px;
    width: 6px; height: 10px;
    border: 2px solid #fff;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
}

.chat-item.selected-item {
    background: rgba(37, 211, 102, 0.12);
    border-radius: 8px;
    padding: 4px 6px;
    margin: 0 -6px;
    transition: background 0.15s ease;
}

.selection-mode-active .msg-actions { display: none !important; }

.selection-mode-active .bubble {
    cursor: pointer;
    user-select: none;
}

/* Selection toolbar */
#selectionBar {
    height: 64px;
    background: #128C7E;
    display: none;
    align-items: center;
    padding: 0 12px;
    gap: 12px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    animation: slideDown 0.2s cubic-bezier(0.4,0,0.2,1);
}

#selectionBar.show { display: flex; }

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

#selectionBar .sel-back {
    background: none; border: none; color: #fff;
    font-size: 1.2rem; cursor: pointer; border-radius: 50%;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s; flex-shrink: 0;
}
#selectionBar .sel-back:hover { background: rgba(255,255,255,0.15); }

#selectionBar .sel-count {
    font-size: 1rem; font-weight: 600; color: #fff; flex-grow: 1;
}

#selectionBar .sel-actions {
    display: flex; align-items: center; gap: 4px;
}

#selectionBar .sel-btn {
    background: none; border: none; color: #fff;
    font-size: 1.15rem; cursor: pointer; border-radius: 50%;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s;
}
#selectionBar .sel-btn:hover { background: rgba(255,255,255,0.15); }
#selectionBar .sel-btn:disabled {
    opacity: 0.35; cursor: not-allowed; pointer-events: none;
}
</style>

<div class="container py-4">
    <div class="card shadow" style="height: 600px; border-radius: 12px; overflow: hidden; border: none;">
        <div class="row g-0 h-100">

            {{-- KIRI: DAFTAR KONTAK --}}
            <div class="col-md-4 border-end d-flex flex-column" style="background: #f8f9fa;">

                <div class="p-3 border-bottom bg-white">
                    <h6 class="fw-bold mb-3 text-success">
                        <i class="bi bi-chat-dots-fill me-2"></i>Pesan
                    </h6>
                    <input type="text" class="form-control form-control-sm"
                           placeholder="Cari kontak..." id="searchInput" oninput="filterContacts()">
                </div>

                <div class="flex-grow-1 overflow-auto" id="contactList">
                    @forelse($users as $user)
                        @php $isActive = isset($receiver) && $receiver->id == $user->id; @endphp
                        <a href="{{ route('chat.show', $user->id) }}"
                           class="contact-item list-group-item list-group-item-action border-0 px-3 py-2 {{ $isActive ? 'active-contact' : '' }}"
                           style="text-decoration: none;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle overflow-hidden flex-shrink-0"
                                     style="width:42px; height:42px;">
                                    @if(!empty($user->avatar))
                                        <img src="{{ Str::startsWith($user->avatar, ['http://', 'https://']) ? $user->avatar : asset('storage/' . $user->avatar) }}"
                                             style="width:100%;height:100%;object-fit:cover;" alt="{{ $user->name }}">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center fw-bold text-white"
                                             style="background: #{{ substr(md5($user->name), 0, 6) }}; font-size: 1rem;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-truncate">
                                    <div class="fw-semibold text-truncate" style="font-size: 0.9rem;">{{ $user->name }}</div>
                                    <small class="{{ $isActive ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.75rem;">
                                        Klik untuk chat
                                    </small>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted p-4">
                            <i class="bi bi-person-x" style="font-size: 2rem;"></i>
                            <p class="mt-2 small">Tidak ada kontak</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- KANAN: RUANG CHAT --}}
            <div class="col-md-8 d-flex flex-column">

                @if(isset($receiver))

                    {{-- Header chat --}}
                    <div id="chatHeader" class="p-3 bg-white border-bottom d-flex align-items-center gap-3"
                         style="box-shadow: 0 1px 3px rgba(0,0,0,0.06); height: 64px; flex-shrink: 0;">
                        <div class="rounded-circle overflow-hidden flex-shrink-0" style="width:40px; height:40px;">
                            @if(!empty($receiver->avatar))
                                <img src="{{ Str::startsWith($receiver->avatar, ['http://', 'https://']) ? $receiver->avatar : asset('storage/' . $receiver->avatar) }}"
                                     style="width:100%;height:100%;object-fit:cover;" alt="{{ $receiver->name }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center fw-bold text-white"
                                     style="background: #128C7E; font-size: 1.1rem;">
                                    {{ strtoupper(substr($receiver->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size: 0.95rem;">{{ $receiver->name }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Online</small>
                        </div>
                    </div>

                    {{-- Selection bar --}}
                    <div id="selectionBar">
                        <button class="sel-back" id="btnCancelSelection" title="Batal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <span class="sel-count" id="selectedTotal">0 dipilih</span>
                        <div class="sel-actions">
                            <button class="sel-btn" id="btnDeleteSelected" title="Hapus" disabled>
                                <i class="bi bi-trash2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Chat box --}}
                    <div class="chat-box flex-grow-1 p-3" id="chatBox">
                        <div class="d-flex flex-column gap-2" id="chatMessagesContainer">

                            <div class="text-center my-2">
                                <span class="bg-white px-3 py-1 rounded-pill shadow-sm small text-muted"
                                      style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                                </span>
                            </div>

                            @foreach($chats as $chat)
                                @php $isMe = $chat->sender_id == auth()->id(); @endphp

                                <div class="chat-item d-flex align-items-start gap-2 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}"
                                     id="chat-row-{{ $chat->id }}"
                                     data-id="{{ $chat->id }}"
                                     data-is-me="{{ $isMe ? '1' : '0' }}">

                                    <div class="select-mode">
                                        <input type="checkbox" class="chat-checkbox" value="{{ $chat->id }}">
                                    </div>

                                    <div class="msg-container">
                                        <div class="msg-wrap d-flex align-items-center {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}">

                                            <div class="bubble {{ $isMe ? 'me' : 'you' }}">
                                                @if($chat->image)
                                                    <img src="{{ asset('storage/' . $chat->image) }}"
                                                         class="img-fluid rounded mb-2"
                                                         style="max-width: 220px; display: block;">
                                                @endif
                                                @if($chat->message)
                                                    <div>{{ $chat->message }}</div>
                                                @endif
                                                <div class="time">{{ $chat->created_at->format('H:i') }}</div>
                                            </div>

                                            <div class="msg-actions dropdown mx-1">
                                                <button class="btn btn-sm border-0 bg-transparent p-1"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-sm shadow-sm" style="min-width: 160px;">
                                                    <li>
                                                        <a class="dropdown-item btn-select-message"
                                                           href="javascript:void(0)" data-id="{{ $chat->id }}">
                                                            <i class="bi bi-check2-square me-2"></i>Pilih
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item btn-copy-message"
                                                           href="javascript:void(0)" data-message="{{ $chat->message }}">
                                                            <i class="bi bi-clipboard me-2"></i>Salin
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger btn-delete"
                                                           href="javascript:void(0)"
                                                           data-id="{{ $chat->id }}"
                                                           data-is-me="{{ $isMe ? '1' : '0' }}">
                                                            <i class="bi bi-trash me-2"></i>Hapus
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            @endforeach

                        </div>
                    </div>

                    {{-- Input form --}}
                    <div class="p-3 bg-white border-top">
                        <form id="chatForm" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex align-items-center gap-2">
                                <label class="btn btn-outline-secondary btn-sm mb-0 px-2" title="Kirim gambar">
                                    <i class="bi bi-image"></i>
                                    <input type="file" id="imageInput" name="image" accept="image/*" hidden>
                                </label>
                                <div id="previewContainer"></div>
                                <input type="text" id="messageInput" name="message"
                                       class="form-control form-control-sm"
                                       placeholder="Tulis pesan..." autocomplete="off"
                                       style="border-radius: 20px; padding: 8px 16px;">
                                <button type="submit" class="btn btn-success btn-sm px-3" id="btnSend"
                                        style="border-radius: 20px; white-space: nowrap;">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                @else
                    <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center text-muted"
                         style="background: #f0f2f5;">
                        <div class="text-center p-4">
                            <i class="bi bi-chat-square-dots" style="font-size: 3.5rem; color: #ccc;"></i>
                            <h6 class="mt-3 fw-semibold">Selamat Datang</h6>
                            <p class="small text-muted mb-0">Pilih kontak di sebelah kiri untuk mulai percakapan</p>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>

<script>
function filterContacts() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    document.querySelectorAll('.contact-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = (keyword === '' || text.includes(keyword)) ? '' : 'none';
    });
}
</script>

@if(isset($receiver))
<script>
document.addEventListener('DOMContentLoaded', function () {

    const chatBox               = document.getElementById('chatBox');
    const chatMessagesContainer = document.getElementById('chatMessagesContainer');
    const chatForm              = document.getElementById('chatForm');
    const messageInput          = document.getElementById('messageInput');
    const imageInput            = document.getElementById('imageInput');
    const previewContainer      = document.getElementById('previewContainer');
    const selectionBar          = document.getElementById('selectionBar');
    const selectedTotal         = document.getElementById('selectedTotal');
    const btnDeleteSelected     = document.getElementById('btnDeleteSelected');
    const btnCancelSelection    = document.getElementById('btnCancelSelection');

    const authUserId = "{{ auth()->id() }}";
    const receiverId = "{{ $receiver->id }}";
    const csrfToken  = "{{ csrf_token() }}";

    // ─────────────────────────────────────────────────────────
    // Auto scroll
    // ─────────────────────────────────────────────────────────
    function scrollToBottom() {
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    }
    scrollToBottom();

    // ─────────────────────────────────────────────────────────
    // Realtime via Laravel Echo (Reverb)
    // ─────────────────────────────────────────────────────────
    if (typeof Echo !== 'undefined' && authUserId) {

        Echo.channel('chat.' + authUserId)

            // ── Pesan masuk baru ──────────────────────────────
            .listen('.MessageSent', (e) => {
                if (String(e.chat.sender_id) === String(receiverId)) {
                    if (!document.getElementById('chat-row-' + e.chat.id)) {
                        appendChatBubble(e.chat, false);
                    }
                }
            })

            // ── Pesan dihapus (scope=all) dari lawan bicara ──
            .listen('.MessageDeleted', (e) => {
                // Hanya hapus dari DOM jika scope = 'all'
                // scope = 'me' tidak perlu broadcast ke pihak lain
                if (e.scope === 'all') {
                    const el = document.getElementById('chat-row-' + e.chat_id);
                    if (el) {
                        // Animasi sebelum dihapus
                        el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                        el.style.opacity    = '0';
                        el.style.transform  = 'scale(0.95)';
                        setTimeout(() => el.remove(), 250);
                    }
                }
            });
    }

    // ─────────────────────────────────────────────────────────
    // Kirim pesan
    // ─────────────────────────────────────────────────────────
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const hasMessage = messageInput.value.trim() !== '';
        const hasImage   = imageInput.files.length > 0;
        if (!hasMessage && !hasImage) return;

        const btnSend = document.getElementById('btnSend');
        btnSend.disabled = true;

        const formData = new FormData(chatForm);

        fetch("{{ route('chat.send', $receiver->id) }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (!document.getElementById('chat-row-' + data.chat.id)) {
                    appendChatBubble(data.chat, true);
                }
                messageInput.value = '';
                imageInput.value   = '';
                previewContainer.innerHTML = '';
            } else {
                alert(data.error ?? 'Gagal mengirim pesan.');
            }
        })
        .catch(err => { console.error('Send error:', err); alert('Terjadi kesalahan saat mengirim pesan.'); })
        .finally(() => { btnSend.disabled = false; messageInput.focus(); });
    });

    // ─────────────────────────────────────────────────────────
    // Buat bubble HTML
    // ─────────────────────────────────────────────────────────
    function appendChatBubble(chat, isMe) {
        const alignClass  = isMe ? 'justify-content-end' : 'justify-content-start';
        const wrapClass   = isMe ? 'flex-row-reverse'    : 'flex-row';
        const bubbleClass = isMe ? 'me'                  : 'you';

        let timeStr = 'Baru saja';
        if (chat.created_at) {
            const d = new Date(chat.created_at);
            timeStr = String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
        }

        const imageHtml = chat.image
            ? `<img src="/storage/${chat.image}" class="img-fluid rounded mb-2" style="max-width:220px;display:block;">`
            : '';

        const msgHtml = chat.message ? `<div>${escapeHtml(chat.message)}</div>` : '';

        const html = `
            <div class="chat-item d-flex align-items-start gap-2 ${alignClass}"
                 id="chat-row-${chat.id}" data-id="${chat.id}" data-is-me="${isMe ? '1' : '0'}">
                <div class="select-mode">
                    <input type="checkbox" class="chat-checkbox" value="${chat.id}">
                </div>
                <div class="msg-container">
                    <div class="msg-wrap d-flex align-items-center ${wrapClass}">
                        <div class="bubble ${bubbleClass}">
                            ${imageHtml}
                            ${msgHtml}
                            <div class="time">${timeStr}</div>
                        </div>
                        <div class="msg-actions dropdown mx-1">
                            <button class="btn btn-sm border-0 bg-transparent p-1" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical text-muted"></i>
                            </button>
                            <ul class="dropdown-menu shadow-sm" style="min-width:140px;font-size:0.85rem;">
                                <li>
                                    <a class="dropdown-item btn-select-message"
                                       href="javascript:void(0)" data-id="${chat.id}">
                                        <i class="bi bi-check2-square me-2"></i>Pilih
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item btn-copy-message"
                                       href="javascript:void(0)" data-message="${escapeAttr(chat.message ?? '')}">
                                        <i class="bi bi-clipboard me-2"></i>Salin
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger btn-delete"
                                       href="javascript:void(0)" data-id="${chat.id}" data-is-me="${isMe ? '1' : '0'}">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>`;

        chatMessagesContainer.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function escapeAttr(str) { return String(str).replace(/"/g,'&quot;'); }

    // ─────────────────────────────────────────────────────────
    // Hapus satu pesan (via dropdown)
    // ─────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete');
        if (!btn || isSelectionMode()) return;
        e.preventDefault();

        const id   = btn.getAttribute('data-id');
        const isMe = btn.getAttribute('data-is-me') === '1';

        showDeleteConfirm(1, isMe, function (scope) {
            fetch(`/chat/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ scope: scope })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Hapus dari DOM milik kita langsung (tanpa tunggu broadcast)
                    const el = document.getElementById(`chat-row-${id}`);
                    if (el) {
                        el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                        el.style.opacity    = '0';
                        el.style.transform  = 'scale(0.95)';
                        setTimeout(() => el.remove(), 250);
                    }
                    showToast('Pesan dihapus');
                } else {
                    alert(data.message ?? 'Gagal menghapus pesan.');
                }
            })
            .catch(() => alert('Terjadi kesalahan.'));
        });
    });

    // ─────────────────────────────────────────────────────────
    // Salin pesan
    // ─────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-copy-message');
        if (!btn) return;
        e.preventDefault();
        const text = btn.getAttribute('data-message');
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => showToast('Pesan disalin!'))
            .catch(() => alert('Gagal menyalin pesan.'));
    });

    // ─────────────────────────────────────────────────────────
    // Mode seleksi
    // ─────────────────────────────────────────────────────────
    function isSelectionMode() { return selectionBar.classList.contains('show'); }

    function enterSelectionMode() {
        chatMessagesContainer.classList.add('selection-mode-active');
        document.getElementById('chatHeader').style.display = 'none';
        selectionBar.classList.add('show');
        updateSelectionBar();
    }

    function exitSelectionMode() {
        chatMessagesContainer.classList.remove('selection-mode-active');
        document.getElementById('chatHeader').style.display = '';
        selectionBar.classList.remove('show');
        document.querySelectorAll('.chat-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.chat-item.selected-item').forEach(el => el.classList.remove('selected-item'));
        updateSelectionBar();
    }

    function updateSelectionBar() {
        const count = document.querySelectorAll('.chat-checkbox:checked').length;
        selectedTotal.textContent = count === 0 ? '0 dipilih' : `${count} dipilih`;
        btnDeleteSelected.disabled = count === 0;
    }

    // Klik "Pilih" di dropdown
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-select-message');
        if (!btn) return;
        e.preventDefault();
        const id = btn.getAttribute('data-id');
        enterSelectionMode();
        const cb = document.querySelector(`.chat-checkbox[value="${id}"]`);
        if (cb) {
            cb.checked = true;
            document.getElementById('chat-row-' + id)?.classList.add('selected-item');
            updateSelectionBar();
        }
    });

    // Klik bubble saat mode seleksi → toggle
    document.addEventListener('click', function (e) {
        if (!isSelectionMode()) return;
        const bubble = e.target.closest('.bubble');
        if (!bubble) return;
        const chatItem = bubble.closest('.chat-item');
        if (!chatItem) return;
        const cb = chatItem.querySelector('.chat-checkbox');
        if (!cb) return;
        cb.checked = !cb.checked;
        chatItem.classList.toggle('selected-item', cb.checked);
        updateSelectionBar();
    });

    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('chat-checkbox')) return;
        e.target.closest('.chat-item')?.classList.toggle('selected-item', e.target.checked);
        updateSelectionBar();
    });

    btnCancelSelection?.addEventListener('click', exitSelectionMode);

    // ─────────────────────────────────────────────────────────
    // Hapus bulk (selection mode)
    // ─────────────────────────────────────────────────────────
    btnDeleteSelected?.addEventListener('click', function () {
        const selected = [...document.querySelectorAll('.chat-checkbox:checked')].map(cb => cb.value);
        if (selected.length === 0) return;

        const allMine = selected.every(id => {
            const row = document.getElementById('chat-row-' + id);
            return row && row.getAttribute('data-is-me') === '1';
        });

        showDeleteConfirm(selected.length, allMine, function (scope) {
            fetch('/chat/delete-selected', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: selected, scope: scope })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Hapus dari DOM milik kita (broadcast handle sisi receiver)
                    selected.forEach(id => {
                        const el = document.getElementById(`chat-row-${id}`);
                        if (el) {
                            el.style.transition = 'opacity 0.2s ease';
                            el.style.opacity    = '0';
                            setTimeout(() => el.remove(), 200);
                        }
                    });
                    exitSelectionMode();
                    showToast(`${selected.length} pesan dihapus`);
                } else {
                    alert(data.message ?? 'Gagal menghapus pesan.');
                }
            })
            .catch(() => alert('Terjadi kesalahan.'));
        });
    });

    // ─────────────────────────────────────────────────────────
    // Preview gambar
    // ─────────────────────────────────────────────────────────
    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        previewContainer.innerHTML = '';
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            previewContainer.innerHTML = `
                <div style="position:relative;">
                    <img src="${e.target.result}" class="preview-img" alt="preview">
                    <button type="button" id="btnRemoveImage"
                        style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;
                               border:none;border-radius:50%;background:#dc3545;color:#fff;
                               font-size:11px;display:flex;align-items:center;justify-content:center;
                               cursor:pointer;line-height:1;">x</button>
                </div>`;
            document.getElementById('btnRemoveImage').addEventListener('click', () => {
                imageInput.value = '';
                previewContainer.innerHTML = '';
            });
        };
        reader.readAsDataURL(file);
        messageInput.focus();
    });

    // ─────────────────────────────────────────────────────────
    // Toast notifikasi
    // ─────────────────────────────────────────────────────────
    function showToast(msg) {
        const toast = document.createElement('div');
        toast.textContent = msg;
        toast.style.cssText = `
            position:fixed; bottom:80px; left:50%; transform:translateX(-50%);
            background:rgba(0,0,0,0.75); color:#fff; padding:6px 16px;
            border-radius:20px; font-size:0.8rem; z-index:9999;
            opacity:1; transition:opacity 0.4s ease;`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 1800);
    }

    // ─────────────────────────────────────────────────────────
    // Dialog konfirmasi hapus (WhatsApp style)
    // showDeleteAll = true  → tampilkan "Hapus untuk semua"
    // showDeleteAll = false → hanya "Hapus untuk saya"
    // ─────────────────────────────────────────────────────────
    function showDeleteConfirm(count, showDeleteAll, onConfirm) {
        document.getElementById('wa-delete-dialog')?.remove();

        const overlay = document.createElement('div');
        overlay.id = 'wa-delete-dialog';
        overlay.style.cssText = `
            position:fixed; inset:0; z-index:9998;
            background:rgba(0,0,0,0.45);
            display:flex; align-items:flex-end; justify-content:center;
            animation:fadeInOverlay 0.18s ease;`;

        const deleteAllBtn = showDeleteAll
            ? `<button id="wa-confirm-delete-all"
                    style="width:100%;padding:15px 24px;border:none;background:none;
                           font-size:0.95rem;font-weight:600;color:#dc3545;cursor:pointer;
                           transition:background 0.15s;"
                    onmouseover="this.style.background='#fff5f5'"
                    onmouseout="this.style.background='none'">
                    <i class="bi bi-people me-2"></i>Hapus untuk semua
               </button>`
            : '';

        overlay.innerHTML = `
            <style>
                @keyframes fadeInOverlay { from{opacity:0} to{opacity:1} }
                @keyframes slideUpSheet  { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
                #wa-delete-sheet { animation:slideUpSheet 0.2s cubic-bezier(0.4,0,0.2,1); }
            </style>
            <div id="wa-delete-sheet"
                 style="background:#fff; border-radius:16px 16px 0 0;
                        width:100%; max-width:520px; padding:20px 0 8px;
                        box-shadow:0 -4px 24px rgba(0,0,0,0.12);">
                <div style="text-align:center; padding:0 24px 16px; border-bottom:1px solid #f0f0f0;">
                    <div style="width:48px;height:48px;border-radius:50%;background:#ffeaea;
                                display:flex;align-items:center;justify-content:center;
                                margin:0 auto 10px; font-size:1.4rem;">🗑️</div>
                    <div style="font-weight:600;font-size:1rem;color:#111;margin-bottom:4px;">
                        Hapus ${count} pesan?
                    </div>
                    <div style="font-size:0.82rem;color:#888;">
                        Pesan yang dihapus tidak dapat dikembalikan.
                    </div>
                </div>
                <button id="wa-confirm-delete"
                    style="width:100%;padding:15px 24px;border:none;background:none;
                           font-size:0.95rem;font-weight:600;color:#dc3545;cursor:pointer;
                           transition:background 0.15s;"
                    onmouseover="this.style.background='#fff5f5'"
                    onmouseout="this.style.background='none'">
                    <i class="bi bi-person me-2"></i>Hapus untuk saya
                </button>
                ${deleteAllBtn}
                <button id="wa-cancel-delete"
                    style="width:100%;padding:14px 24px;border:none;background:none;
                           font-size:0.9rem;color:#555;cursor:pointer;
                           border-top:1px solid #f0f0f0; transition:background 0.15s;"
                    onmouseover="this.style.background='#f8f9fa'"
                    onmouseout="this.style.background='none'">
                    Batal
                </button>
            </div>`;

        document.body.appendChild(overlay);

        document.getElementById('wa-confirm-delete').addEventListener('click', () => {
            overlay.remove();
            onConfirm('me');
        });

        if (showDeleteAll) {
            document.getElementById('wa-confirm-delete-all').addEventListener('click', () => {
                overlay.remove();
                onConfirm('all');
            });
        }

        document.getElementById('wa-cancel-delete').addEventListener('click', () => overlay.remove());
        overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.remove(); });
    }

});
</script>
@endif

@endsection