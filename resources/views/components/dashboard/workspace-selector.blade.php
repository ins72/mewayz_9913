@php
    $workspaces = auth()->user()->workspaces ?? collect();
    $currentWorkspace = auth()->user()->currentWorkspace ?? $workspaces->first();
@endphp

<div class="workspace-selector" x-data="workspaceSelector()" x-init="init()">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-medium text-gray-900">Current Workspace</h3>
        <button @click="showCreateModal = true" class="btn-sm btn-outline-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Workspace
        </button>
    </div>
    
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-semibold text-sm" x-text="currentWorkspace ? currentWorkspace.name.charAt(0).toUpperCase() : 'W'"></span>
        </div>
        <div class="flex-1">
            <select 
                x-model="selectedWorkspaceId" 
                @change="switchWorkspace()"
                class="form-select w-full"
            >
                <template x-for="workspace in workspaces" :key="workspace.id">
                    <option :value="workspace.id" x-text="workspace.name"></option>
                </template>
            </select>
        </div>
    </div>
    
    <div class="mt-3 text-xs text-gray-500">
        <span x-text="currentWorkspace ? currentWorkspace.description : 'No workspace selected'"></span>
    </div>
    
    <!-- Current Workspace Stats -->
    <div class="mt-4 grid grid-cols-3 gap-4 text-center">
        <div>
            <div class="text-lg font-semibold text-gray-900" x-text="currentWorkspace ? currentWorkspace.stats.active_features : 0"></div>
            <div class="text-xs text-gray-500">Active Features</div>
        </div>
        <div>
            <div class="text-lg font-semibold text-gray-900" x-text="currentWorkspace ? currentWorkspace.stats.total_posts : 0"></div>
            <div class="text-xs text-gray-500">Total Posts</div>
        </div>
        <div>
            <div class="text-lg font-semibold text-gray-900" x-text="currentWorkspace ? currentWorkspace.stats.monthly_revenue : '$0'"></div>
            <div class="text-xs text-gray-500">Monthly Revenue</div>
        </div>
    </div>
    
    <!-- Create Workspace Modal -->
    <div x-show="showCreateModal" class="modal-overlay" @click.self="showCreateModal = false">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create New Workspace</h3>
                <button @click="showCreateModal = false" class="modal-close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="createWorkspace()">
                    <div class="form-group">
                        <label class="form-label">Workspace Name</label>
                        <input type="text" x-model="newWorkspace.name" class="form-input" placeholder="Enter workspace name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description (Optional)</label>
                        <textarea x-model="newWorkspace.description" class="form-textarea" placeholder="Describe your workspace"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Business Goals</label>
                        <div class="space-y-2">
                            <template x-for="goal in availableGoals" :key="goal.key">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" :value="goal.key" x-model="newWorkspace.goals" class="form-checkbox">
                                    <span class="text-sm" x-text="goal.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showCreateModal = false" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary" :disabled="!newWorkspace.name || creating">
                            <span x-show="creating">Creating...</span>
                            <span x-show="!creating">Create Workspace</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function workspaceSelector() {
    return {
        workspaces: @json($workspaces),
        currentWorkspace: @json($currentWorkspace),
        selectedWorkspaceId: @json($currentWorkspace?->id),
        showCreateModal: false,
        creating: false,
        newWorkspace: {
            name: '',
            description: '',
            goals: []
        },
        availableGoals: [
            { key: 'instagram', name: 'Instagram Management' },
            { key: 'link_bio', name: 'Link in Bio' },
            { key: 'courses', name: 'Course Creation' },
            { key: 'ecommerce', name: 'E-commerce' },
            { key: 'crm', name: 'CRM & Email Marketing' },
            { key: 'analytics', name: 'Analytics & Reporting' }
        ],
        
        init() {
            this.loadWorkspaces();
        },
        
        async loadWorkspaces() {
            try {
                const response = await Mewayz.api('/api/workspaces');
                this.workspaces = response.data;
                
                // Update current workspace with stats
                if (this.currentWorkspace) {
                    const currentWs = this.workspaces.find(ws => ws.id === this.currentWorkspace.id);
                    if (currentWs) {
                        this.currentWorkspace = currentWs;
                    }
                }
            } catch (error) {
                console.error('Failed to load workspaces:', error);
            }
        },
        
        async switchWorkspace() {
            if (!this.selectedWorkspaceId) return;
            
            try {
                const response = await Mewayz.api(`/api/workspaces/${this.selectedWorkspaceId}/switch`, {
                    method: 'POST'
                });
                
                if (response.success) {
                    this.currentWorkspace = response.data;
                    Mewayz.notify('Workspace switched successfully', 'success');
                    
                    // Reload the page to update the context
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } catch (error) {
                console.error('Failed to switch workspace:', error);
                Mewayz.notify('Failed to switch workspace', 'error');
            }
        },
        
        async createWorkspace() {
            if (!this.newWorkspace.name || this.creating) return;
            
            this.creating = true;
            
            try {
                const response = await Mewayz.api('/api/workspaces', {
                    method: 'POST',
                    body: JSON.stringify(this.newWorkspace)
                });
                
                if (response.success) {
                    this.workspaces.push(response.data);
                    this.selectedWorkspaceId = response.data.id;
                    this.currentWorkspace = response.data;
                    this.showCreateModal = false;
                    this.newWorkspace = { name: '', description: '', goals: [] };
                    
                    Mewayz.notify('Workspace created successfully', 'success');
                    
                    // Switch to the new workspace
                    await this.switchWorkspace();
                }
            } catch (error) {
                console.error('Failed to create workspace:', error);
                Mewayz.notify('Failed to create workspace', 'error');
            } finally {
                this.creating = false;
            }
        }
    }
}
</script>