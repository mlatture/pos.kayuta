@extends('layouts.admin')

@section('title','Content Ideas')

@section('content')
    {{-- CSRF meta (agar layout me already ho to bhi safe hai) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card shadow border-0 bg-white rounded-4 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #00b09b, #96c93d);">
            <h4 class="mb-0">
                <i class="bi bi-lightbulb me-2"></i> Content Ideas
            </h4>
            <a href="{{ route('admin.content-hub.settings',['tab'=>'overview']) }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-gear"></i> Settings
            </a>
        </div>

        <div class="card-body">
            <div id="ideas-app" class="position-relative">

                {{-- FULL-SCREEN OVERLAY LOADER --}}
                <div v-if="actionLoading"
                     class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                     style="background: rgba(255,255,255,0.8); z-index: 1050;">
                    <div class="text-center">
                        <div class="spinner-border text-success mb-2" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <div class="fw-semibold text-muted">Processing… please wait</div>
                    </div>
                </div>

                {{-- Loading state --}}
                <div v-if="loading" class="text-muted mb-2">
                    Loading ideas…
                </div>

                {{-- Empty state --}}
                <div v-else-if="ideas.length === 0" class="text-muted mb-2">
                    No ideas yet. Ideas will appear here after the weekly AI generation runs or seeder inserts.
                </div>

                {{-- Ideas grid --}}
                <div v-else class="row g-3">
                    <div class="col-md-6" v-for="idea in ideas" :key="idea.id">
                        <div class="border rounded-3 p-3 h-100 d-flex flex-column justify-content-between bg-light">
                            <div>
                                <h5 class="mb-1 fw-semibold d-flex align-items-center">
                                    <span>@{{ idea.title }}</span>

                                    {{-- Status badge/tag right side --}}
                                    <span class="badge ms-2"
                                          :class="{
                                            'bg-secondary': idea.status === 'draft',
                                            'bg-success': idea.status === 'approved',
                                            'bg-danger': idea.status === 'rejected'
                                          }">
                                        @{{ idea.status === 'approved'
                                            ? 'Approved'
                                            : (idea.status === 'draft' ? 'Draft' : 'Rejected') }}
                                    </span>
                                </h5>

                                <p class="mb-2 small text-muted">
                                    @{{ idea.summary }}
                                </p>

                                <div class="small text-secondary mb-2">
                                    <span class="me-2">
                                        <i class="bi bi-tag"></i>
                                        Category: @{{ idea.category && idea.category.name ? idea.category.name : '—' }}
                                    </span>
                                    <span>
                                        <i class="bi bi-circle-fill"
                                           :class="{
                                             'text-secondary': idea.status === 'draft',
                                             'text-success': idea.status === 'approved',
                                             'text-danger': idea.status === 'rejected'
                                           }"
                                           style="font-size: 0.5rem;"></i>
                                        Status: @{{ idea.status }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-2">
                                <button class="btn btn-sm btn-success"
                                        :disabled="actionLoading"
                                        @click="approve(idea)">
                                    <i class="bi bi-check2-circle"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-warning text-white"
                                        :disabled="actionLoading"
                                        @click="replaceIdea(idea)">
                                    <i class="bi bi-arrow-repeat"></i> Replace
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-auto"
                                        :disabled="actionLoading"
                                        @click="deleteIdea(idea)">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div> {{-- row --}}
            </div> {{-- #ideas-app --}}
        </div>
    </div>

    {{-- Vue 3 + Axios CDN – yahan hi include kar rahe hain, taake layout ka @stack issue na aaye --}}
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const { createApp, ref, onMounted } = Vue

            createApp({
                setup() {
                    const ideas         = ref([])
                    const loading       = ref(false)   // for initial + list loading
                    const actionLoading = ref(false)   // for full-screen overlay on actions

                    const csrfMeta = document.querySelector('meta[name="csrf-token"]')
                    if (csrfMeta) {
                        const csrf = csrfMeta.getAttribute('content')
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf
                    }
                    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

                    const loadIdeas = async () => {
                        try {
                            loading.value = true
                            const { data } = await axios.get('/api/ideas')
                            ideas.value = data
                        } catch (e) {
                            console.error('Failed to load ideas', e)
                            alert('Error loading ideas (see console).')
                        } finally {
                            loading.value = false
                        }
                    }

                    const approve = async (idea) => {
                        if (!confirm('Approve this idea and send it into the post-generation workflow?')) return

                        try {
                            actionLoading.value = true
                            await axios.post(`/api/ideas/${idea.id}/approve`)
                            await loadIdeas()
                        } catch (e) {
                            console.error('Failed to approve idea', e)
                            alert('Error approving idea (see console).')
                        } finally {
                            actionLoading.value = false
                        }
                    }

                    const replaceIdea = async (idea) => {
                        if (!confirm('Replace this idea with a new one?')) return

                        try {
                            actionLoading.value = true
                            await axios.post(`/api/ideas/${idea.id}/replace`)
                            await loadIdeas()
                        } catch (e) {
                            console.error('Failed to replace idea', e)
                            alert('Error replacing idea (see console).')
                        } finally {
                            actionLoading.value = false
                        }
                    }

                    const deleteIdea = async (idea) => {
                        if (!confirm('Delete this idea permanently?')) return

                        try {
                            actionLoading.value = true
                            await axios.delete(`/api/ideas/${idea.id}`)
                            await loadIdeas()
                        } catch (e) {
                            console.error('Failed to delete idea', e)
                            alert('Error deleting idea (see console).')
                        } finally {
                            actionLoading.value = false
                        }
                    }

                    onMounted(loadIdeas)

                    return {
                        ideas,
                        loading,
                        actionLoading,
                        approve,
                        replaceIdea,
                        deleteIdea,
                    }
                }
            }).mount('#ideas-app')
        });
    </script>
@endsection
