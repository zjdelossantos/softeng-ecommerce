function showSection(sectionId) {
    // Get all sections
    const personalDetails = document.getElementById('personalDetails');
    const orders = document.getElementById('orders');

    // Hide both sections
    personalDetails.style.display = 'none';
    orders.style.display = 'none';

    // Show the selected section
    document.getElementById(sectionId).style.display = 'block';
}
