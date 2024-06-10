// notes.js

document.addEventListener('DOMContentLoaded', function () {
    const notesContainer = document.querySelector('.notes-container');

    // Încărcarea notițelor din localStorage
    const notes = JSON.parse(localStorage.getItem('notes')) || [];

    // Funcție pentru a afișa notițele
    function displayNotes(notes) {
        notesContainer.innerHTML = ''; // Golește containerul înainte de a adăuga notițele
        notes.forEach((note, index) => {
            const noteCard = document.createElement('div');
            noteCard.className = 'note-card';
            noteCard.innerHTML = `
                <h2>Note ${index + 1}</h2>
                <p>${note.content}</p>
            `;
            notesContainer.appendChild(noteCard);
        });
    }

    // Afișează notițele inițiale
    displayNotes(notes);
});
