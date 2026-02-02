// Script spécifique pour le dashboard Agent
class AgentDashboard extends Dashboard {
    constructor() {
        super();
        this.initAgentFeatures();
    }
    
    initAgentFeatures() {
        this.initQuickActions();
        this.loadAgentData();
    }
    
    initQuickActions() {
        const quickButtons = document.querySelectorAll('.quick-btn');
        quickButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.textContent.trim();
                console.log(`Action: ${action}`);
                
                if (action.includes('Nouvelle Demande')) {
                    // Redirection vers nouvelle demande
                    console.log('Nouvelle demande');
                } else if (action.includes('Signaler Problème')) {
                    // Redirection vers nouvelle réclamation
                    console.log('Nouvelle réclamation');
                }
            });
        });
    }
    
    loadAgentData() {
        // Simulation de chargement des données agent
        setTimeout(() => {
            this.updateStats(8, 2, 3, 1);
            this.showAlert('success', 'Bienvenue!', 'Tableau de bord chargé avec succès');
        }, 1000);
    }
}

// Initialisation quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    const agentDashboard = new AgentDashboard();
});