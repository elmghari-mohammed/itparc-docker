// Scripts spécifiques à la page d'ajout d'équipement

// Cette fonction peut être utilisée pour des validations supplémentaires
function validateEquipmentForm() {
    const serialNumber = document.getElementById('serialNumber').value.trim();
    const marque = document.getElementById('marque').value.trim();
    const modele = document.getElementById('modele').value.trim();
    const type = document.getElementById('type').value;
    const depot = document.getElementById('depot').value;
    
    // Validation basique côté client
    if (!serialNumber || !marque || !modele || !type || !depot) {
        showMessage('error', 'Veuillez remplir tous les champs obligatoires.');
        return false;
    }
    
    return true;
}

// Ajouter la validation au formulaire
document.getElementById('equipmentForm').addEventListener('submit', function(e) {
    if (!validateEquipmentForm()) {
        e.preventDefault();
    }
});

