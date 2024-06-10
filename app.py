from flask import Flask, request, jsonify # biblioteci pt server
from flask_cors import CORS # biblioteca pt a permite accesul din alte domenii
import tensorflow as tf # biblioteca pt machine learning
from tensorflow.keras.preprocessing.text import Tokenizer # biblioteca pt preprocesare text
from tensorflow.keras.preprocessing.sequence import pad_sequences # biblioteca pt pad sequențe
from tensorflow.keras.models import Sequential # biblioteca pt modelul secvențial
from tensorflow.keras.layers import Embedding, LSTM, Dense, Dropout, TimeDistributed, Bidirectional # biblioteca pt straturile modelului
from tensorflow.keras.utils import to_categorical # biblioteca pt one-hot encoding
import numpy as np # biblioteca pt lucrul cu matrice
import re # biblioteca pt expresii regulate
import random # biblioteca pt generarea de numere random

app = Flask(__name__)
CORS(app)

# Datele de antrenare (conversații)
conversations = [
    ("Ce este un tablou (array) în programare?", "Un tablou (array) este o structură de date care stochează o colecție de elemente de același tip într-o ordine specificată."),
    ("Ce este o buclă repetitivă?", "O buclă repetitivă este o construcție de programare care permite executarea repetată a unui bloc de cod până când o condiție specificată este îndeplinită."),
    ("Ce este un algoritm?", "Un algoritm este un set de instrucțiuni pas cu pas folosite pentru a rezolva o problemă sau pentru a realiza o sarcină."),
    ("Ce este o variabilă în programare?", "O variabilă este un spațiu de memorie denumit folosit pentru a stoca valori care pot fi modificate în timpul execuției unui program."),
    ("Ce este o funcție?", "O funcție este un bloc de cod care îndeplinește o sarcină specifică și poate fi apelat de oriunde din program."),
    ("Ce este un obiect în programare orientată pe obiecte?", "Un obiect este o instanță a unei clase care conține atât date (atribute) cât și funcții (metode) care operează asupra acestor date."),
    ("Ce este o clasă în programare orientată pe obiecte?", "O clasă este un șablon sau o structură care definește atributele și metodele comune ale unui set de obiecte."),
    ("Ce este recursivitatea?", "Recursivitatea este o tehnică de programare în care o funcție se apelează pe ea însăși pentru a rezolva o problemă."),
    ("Ce este o condiție if?", "O condiție if este o instrucțiune care permite executarea unui bloc de cod dacă o anumită condiție este adevărată."),
    ("Ce este o buclă for?", "O buclă for este o construcție de programare care permite repetarea unui bloc de cod pentru un număr specificat de ori sau pentru fiecare element dintr-o colecție."),
    ("Ce este un IDE?", "Un IDE (Integrated Development Environment) este un mediu de dezvoltare software care oferă instrumente și facilități pentru scrierea, testarea și depanarea codului."),
    ("Ce este depurarea (debugging)?", "Depurarea (debugging) este procesul de identificare și corectare a erorilor (bug-urilor) dintr-un program."),
    ("Ce este Git?", "Git este un sistem de control al versiunilor distribuit folosit pentru a urmări modificările din fișierele de cod sursă în timpul dezvoltării software."),
    ("Ce este un repository în Git?", "Un repository în Git este un depozit sau un loc unde sunt stocate toate fișierele și istoricul modificărilor acestora."),
    ("Ce este un branch în Git?", "Un branch în Git este o ramură separată de dezvoltare care permite lucrul pe o funcționalitate sau o corecție fără a afecta ramura principală."),
    ("Ce este un commit în Git?", "Un commit în Git este o înregistrare a modificărilor făcute într-un repository."),
    ("Ce este o interfață grafică (GUI)?", "O interfață grafică (GUI) este un tip de interfață care permite utilizatorilor să interacționeze cu un program prin elemente vizuale precum butoane, ferestre și meniuri."),
    ("Ce este o bază de date?", "O bază de date este o colecție organizată de date stocate și accesate electronic."),
    ("Ce este SQL?", "SQL (Structured Query Language) este un limbaj de programare utilizat pentru gestionarea și manipularea bazelor de date relaționale."),
    ("Ce este o cerere (query) în SQL?", "O cerere (query) în SQL este o instrucțiune utilizată pentru a extrage sau manipula date dintr-o bază de date."),
    ("Ce este HTML?", "HTML (HyperText Markup Language) este limbajul standard pentru crearea și structura paginilor web."),
    ("Ce este CSS?", "CSS (Cascading Style Sheets) este un limbaj de stilizare folosit pentru a descrie aspectul și formatarea unui document scris în HTML."),
    ("Ce este JavaScript?", "JavaScript este un limbaj de programare folosit pentru a crea conținut dinamic și interactiv pe paginile web."),
    ("Ce este un API?", "Un API (Application Programming Interface) este un set de funcții și proceduri care permit crearea de aplicații ce accesează date sau servicii din alte aplicații."),
    ("Ce este JSON?", "JSON (JavaScript Object Notation) este un format ușor de schimb de date, care este ușor de citit și scris atât pentru oameni, cât și pentru mașini."),
    ("Ce este un server web?", "Un server web este un software care servește pagini web utilizatorilor la cerere prin intermediul unui browser."),
    ("Ce este un protocol HTTP?", "HTTP (HyperText Transfer Protocol) este protocolul folosit pentru a transfera pagini web de la un server la un client."),
    ("Ce este Python?", "Python este un limbaj de programare interpretat, de nivel înalt și de uz general."),
    ("Ce este un API REST?", "Un API REST (Representational State Transfer) este un tip de API care utilizează HTTP pentru a efectua operațiuni CRUD (Create, Read, Update, Delete) pe resurse."),
    ("Ce este un framework?", "Un framework este un cadru de lucru care oferă suportul necesar pentru dezvoltarea aplicațiilor software."),
    ("Ce este HTML?", "Este limbajul standard pentru crearea și structura paginilor web."),
    ("Ce este CSS?", "Descrie aspectul și formatarea unui document scris în HTML."),
    ("Ce este Python?", "Este un limbaj de programare utilizat pentru gestionarea și manipularea datelor."),
    ("Ce este SQL?", "Este un limbaj pentru gestionarea și manipularea bazelor de date relaționale."),
    ("Ce este Git?", "Este un sistem de control al versiunilor care descrie fișierele de cod sursă în timpul dezvoltării software."),
    ("Ce este JSON?", "Este un format de date ușor de citit și de scris utilizat pentru schimbul de date între un server și un client."),
    ("Ce este un framework?", "Este o structură sau un set de instrumente care facilitează dezvoltarea, testarea și implementarea aplicațiilor software."),
    ("Ce este Java?", "Java este un limbaj de programare orientat pe obiecte dezvoltat de Sun Microsystems, care este utilizat pe scară largă pentru dezvoltarea de aplicații web și mobile."),
    ("Ce este JVM?", "JVM (Java Virtual Machine) este o mașină virtuală care permite rularea aplicațiilor Java prin interpretarea codului bytecode."),
    ("Ce este JDK?", "JDK (Java Development Kit) este un pachet software care include un compilator Java, o JVM și alte resurse necesare pentru dezvoltarea și rularea aplicațiilor Java."),
    ("Ce este o clasă în Java?", "O clasă în Java este un șablon din care se creează obiecte, definind atributele și metodele comune acestora."),
    ("Ce este o metodă în Java?", "O metodă în Java este o funcție definită într-o clasă care poate fi apelată pentru a realiza o acțiune specifică."),
    ("Ce este un constructor în Java?", "Un constructor în Java este o metodă specială a unei clase folosită pentru a inițializa obiectele."),
    ("Ce este polimorfismul în Java?", "Polimorfismul în Java este capacitatea de a trata obiecte de diferite clase derivate dintr-o clasă comună prin intermediul unei referințe de tipul clasei părinte."),
    ("Ce este moștenirea în Java?", "Moștenirea în Java este un mecanism prin care o clasă poate moșteni proprietăți și metode de la o altă clasă."),
    ("Ce este o interfață în Java?", "O interfață în Java este un tip de clasă abstractă care poate conține doar semnături de metode și constante."),
    ("Ce este C++?", "C++ este un limbaj de programare de nivel înalt, orientat pe obiecte, derivat din C, utilizat pentru dezvoltarea de aplicații cu performanțe ridicate."),
    ("Ce este o clasă în C++?", "O clasă în C++ este o structură de date care permite gruparea datelor și a funcțiilor care operează pe aceste date."),
    ("Ce este moștenirea în C++?", "Moștenirea în C++ este un mecanism care permite unei clase să dobândească proprietățile și metodele unei alte clase."),
    ("Ce este un constructor în C++?", "Un constructor în C++ este o metodă specială a unei clase care este apelată automat atunci când un obiect al clasei este creat."),
    ("Ce este polimorfismul în C++?", "Polimorfismul în C++ permite tratarea obiectelor de diferite tipuri derivate dintr-o clasă de bază comună ca și cum ar fi de tipul clasei de bază."),
    ("Ce este o funcție virtuală în C++?", "O funcție virtuală în C++ este o metodă care poate fi suprascrisă într-o clasă derivată pentru a implementa un comportament specific."),
    ("Ce este un șablon (template) în C++?", "Un șablon în C++ este o caracteristică care permite definirea de funcții și clase generice care pot lucra cu diferite tipuri de date."),
    ("Ce este STL în C++?", "STL (Standard Template Library) este o bibliotecă standard în C++ care oferă o colecție de clase și funcții generice pentru manipularea de structuri de date."),
    ("Ce este o structură de date?", "O structură de date este o modalitate de organizare și stocare a datelor într-un mod care permite accesul și modificarea eficientă a acestora."),
    ("Ce este o listă simplu înlănțuită?", "O listă simplu înlănțuită este o structură de date în care fiecare element conține o valoare și un pointer către următorul element din listă."),
    ("Ce este o listă dublu înlănțuită?", "O listă dublu înlănțuită este o structură de date în care fiecare element conține o valoare și doi pointeri, unul către elementul anterior și unul către elementul următor."),
    ("Ce este o stivă?", "O stivă este o structură de date care urmează principiul LIFO (Last In, First Out), în care ultimul element adăugat este primul element scos."),
    ("Ce este o coadă?", "O coadă este o structură de date care urmează principiul FIFO (First In, First Out), în care primul element adăugat este primul element scos."),
    ("Ce este un arbore binar?", "Un arbore binar este o structură de date în care fiecare nod are cel mult doi copii, numiți stânga și dreapta."),
    ("Ce este un arbore binar de căutare?", "Un arbore binar de căutare este un arbore binar în care pentru fiecare nod, valorile din subarborele stâng sunt mai mici decât valoarea nodului, iar valorile din subarborele drept sunt mai mari."),
    ("Ce este un algoritm de sortare?", "Un algoritm de sortare este un set de instrucțiuni pentru aranjarea elementelor dintr-o colecție într-o anumită ordine, de obicei crescătoare sau descrescătoare."),
    ("Ce este algoritmul de sortare prin selecție?", "Algoritmul de sortare prin selecție este un algoritm de sortare care împarte lista în două părți, partea sortată și partea nesortată, și repetat selectează cel mai mic (sau cel mai mare) element din partea nesortată și îl plasează la sfârșitul părții sortate."),
    ("Ce este algoritmul de sortare prin inserție?", "Algoritmul de sortare prin inserție este un algoritm de sortare care construiește lista sortată unul câte unul, prin inserarea fiecărui element în poziția corectă."),
    ("Ce este algoritmul de sortare rapidă (quicksort)?", "Algoritmul de sortare rapidă (quicksort) este un algoritm de sortare bazat pe metoda divide et impera, care selectează un pivot și împarte lista în două subliste, una cu elemente mai mici și una cu elemente mai mari decât pivotul, și sortează recursiv sublistele."),
    ("Ce este algoritmul de sortare prin fuziune (mergesort)?", "Algoritmul de sortare prin fuziune (mergesort) este un algoritm de sortare bazat pe metoda divide et impera, care împarte lista în jumătăți mai mici, le sortează recursiv și apoi le îmbină într-o listă sortată."),
    ("Ce este algoritmul de căutare binară?", "Algoritmul de căutare binară este un algoritm eficient de căutare într-o listă sortată, care funcționează prin compararea elementului de căutat cu elementul din mijlocul listei și repetă procesul pe jumătatea corespunzătoare până când elementul este găsit sau lista este epuizată."),
    ("Ce este un graf?", "Un graf este o structură de date compusă din noduri (sau vârfuri) și muchii (sau arce) care leagă perechi de noduri."),
    ("Ce este un arbore în teoria grafurilor?", "Un arbore este un graf neorientat și conex care nu conține cicluri."),
    ("Ce este un algoritm de traversare în adâncime (DFS)?", "Algoritmul de traversare în adâncime (DFS) este un algoritm de parcurgere a unui graf care explorează cât mai adânc posibil de la fiecare nod înainte de a se întoarce și a explora nodurile ramase."),
    ("Ce este un algoritm de traversare în lățime (BFS)?", "Algoritmul de traversare în lățime (BFS) este un algoritm de parcurgere a unui graf care explorează toate nodurile la același nivel înainte de a trece la nivelul următor."),
    ("Ce este un algoritm greedy?", "Un algoritm greedy este un algoritm care face alegeri locale optime la fiecare pas cu speranța că aceste alegeri vor duce la soluția globală optimă."),
    ("Ce este programarea dinamică?", "Programarea dinamică este o tehnică de rezolvare a problemelor complexe prin împărțirea lor în subprobleme mai mici și memorarea soluțiilor subproblemelor pentru a evita recalcularea acestora."),
    ("Ce este un algoritm de sortare stabilă?", "Un algoritm de sortare stabilă este un algoritm de sortare care păstrează ordinea relativă a elementelor egale."),
    ("Ce este un algoritm de sortare instabilă?", "Un algoritm de sortare instabilă este un algoritm de sortare care nu păstrează ordinea relativă a elementelor egale."),
    ("Ce este o listă înlănțuită circulară?", "O listă înlănțuită circulară este o listă înlănțuită în care ultimul nod este conectat înapoi la primul nod, formând un cerc."),
    ("Ce este un arbore AVL?", "Un arbore AVL este un arbore binar de căutare echilibrat în care diferența de înălțime dintre subarborii stâng și drept ai fiecărui nod este cel mult unu."),
    ("Ce este un arbore roșu-negru?", "Un arbore roșu-negru este un tip de arbore binar de căutare autoechilibrat în care fiecare nod are o culoare (roșu sau negru) și respectă anumite reguli de echilibrare."),
    ("Ce este un ciclu într-un graf?", "Un ciclu într-un graf este o secvență de muchii care începe și se termină la același nod, fără a repeta vreo muchie."),
    ("Ce este un graf conex?", "Un graf conex este un graf în care există o cale între oricare două noduri."),
    ("Ce este un graf complet?", "Un graf complet este un graf în care există o muchie între fiecare pereche de noduri."),
    ("Ce este un graf bipartit?", "Un graf bipartit este un graf ale cărui noduri pot fi împărțite în două mulțimi disjuncte astfel încât fiecare muchie să conecteze un nod din prima mulțime cu un nod din a doua mulțime."),
    ("Ce este un graf plan?", "Un graf plan este un graf care poate fi desenat pe o planșă astfel încât muchiile să nu se intersecteze."),
    ("Ce este algoritmul Dijkstra?", "Algoritmul Dijkstra este un algoritm de găsire a celui mai scurt drum de la un nod sursă la toate celelalte noduri dintr-un graf ponderat."),
    ("Ce este algoritmul Floyd-Warshall?", "Algoritmul Floyd-Warshall este un algoritm pentru găsirea celor mai scurte drumuri între toate perechile de noduri dintr-un graf ponderat."),
    ("Ce este algoritmul Bellman-Ford?", "Algoritmul Bellman-Ford este un algoritm pentru găsirea celui mai scurt drum de la un nod sursă la toate celelalte noduri dintr-un graf, care poate gestiona grafuri cu muchii de greutate negativă."),
    ("Ce este un arbore de acoperire minimă?", "Un arbore de acoperire minimă este un subgraf al unui graf conex care include toate nodurile și are suma greutăților muchiilor minimă."),
    ("Ce este algoritmul Prim?", "Algoritmul Prim este un algoritm pentru găsirea arborelui de acoperire minimă într-un graf ponderat."),
    ("Ce este algoritmul Kruskal?", "Algoritmul Kruskal este un algoritm pentru găsirea arborelui de acoperire minimă într-un graf ponderat prin adăugarea repetată a celei mai mici muchii care nu formează un ciclu."),
    ("Ce este căutarea cu backtracking?", "Căutarea cu backtracking este o tehnică de rezolvare a problemelor care implică explorarea tuturor posibilităților prin revenirea asupra deciziilor anterioare când se ajunge într-un impas."),
    ("Ce este un algoritm genetic?", "Un algoritm genetic este o tehnică de optimizare inspirată de procesul de selecție naturală care utilizează operatori genetici precum mutația, încrucișarea și selecția."),
   ("Ce este un algoritm de detectare a anomaliilor?", "Un algoritm de detectare a anomaliilor este un algoritm care identifică modele neobișnuite în date care pot indica evenimente rare sau erori."),
    ("Ce este un algoritm de rețea neurală?", "Un algoritm de rețea neurală este un algoritm inspirat de structura și funcționarea creierului uman, utilizat pentru învățarea automată și recunoașterea de modele complexe."),
    ("Ce este o buclă repetitivă?", "O buclă repetitivă este o construcție de programare care permite executarea repetată a unui bloc de cod până când o condiție specificată este îndeplinită."),
    ("Ce este un algoritm?", "Un algoritm este un set de instrucțiuni pas cu pas folosite pentru a rezolva o problemă sau pentru a realiza o sarcină."),
    ("Ce este o variabilă în programare?", "O variabilă este un spațiu de memorie denumit folosit pentru a stoca valori care pot fi modificate în timpul execuției unui program."),
    ("Ce este o funcție?", "O funcție este un bloc de cod care îndeplinește o sarcină specifică și poate fi apelat de oriunde din program."),
    ("Ce este un obiect în programare orientată pe obiecte?", "Un obiect este o instanță a unei clase care conține atât date (atribute) cât și funcții (metode) care operează asupra acestor date."),
    ("Ce este o clasă în programare orientată pe obiecte?", "O clasă este un șablon sau o structură care definește atributele și metodele comune ale unui set de obiecte."),
    ("Ce este recursivitatea?", "Recursivitatea este o tehnică de programare în care o funcție se apelează pe ea însăși pentru a rezolva o problemă."),
    ("Ce este o condiție if?", "O condiție if este o instrucțiune care permite executarea unui bloc de cod dacă o anumită condiție este adevărată."),
    ("Ce este o buclă for?", "O buclă for este o construcție de programare care permite repetarea unui bloc de cod pentru un număr specificat de ori sau pentru fiecare element dintr-o colecție."),
    ("Ce este un IDE?", "Un IDE (Integrated Development Environment) este un mediu de dezvoltare software care oferă instrumente și facilități pentru scrierea, testarea și depanarea codului."),
    ("Ce este depurarea (debugging)?", "Depurarea (debugging) este procesul de identificare și corectare a erorilor (bug-urilor) dintr-un program."),
    ("Ce este Git?", "Git este un sistem de control al versiunilor distribuit folosit pentru a urmări modificările din fișierele de cod sursă în timpul dezvoltării software."),
    ("Ce este un repository în Git?", "Un repository în Git este un depozit sau un loc unde sunt stocate toate fișierele și istoricul modificărilor acestora."),
    ("Ce este un branch în Git?", "Un branch în Git este o ramură separată de dezvoltare care permite lucrul pe o funcționalitate sau o corecție fără a afecta ramura principală."),
    ("Ce este un commit în Git?", "Un commit în Git este o înregistrare a modificărilor făcute într-un repository."),
    ("Ce este o interfață grafică (GUI)?", "O interfață grafică (GUI) este un tip de interfață care permite utilizatorilor să interacționeze cu un program prin elemente vizuale precum butoane, ferestre și meniuri."),
    ("Ce este o bază de date?", "O bază de date este o colecție organizată de date stocate și accesate electronic."),
    ("Ce este SQL?", "SQL (Structured Query Language) este un limbaj de programare utilizat pentru gestionarea și manipularea bazelor de date relaționale."),
    ("Ce este o cerere (query) în SQL?", "O cerere (query) în SQL este o instrucțiune utilizată pentru a extrage sau manipula date dintr-o bază de date."),
    ("Ce este HTML?", "HTML (HyperText Markup Language) este limbajul standard pentru crearea și structura paginilor web."),
    ("Ce este CSS?", "CSS (Cascading Style Sheets) este un limbaj de stilizare folosit pentru a descrie aspectul și formatarea unui document scris în HTML."),
    ("Ce este JavaScript?", "JavaScript este un limbaj de programare folosit pentru a crea conținut dinamic și interactiv pe paginile web."),
    ("Ce este un API?", "Un API (Application Programming Interface) este un set de funcții și proceduri care permit crearea de aplicații ce accesează date sau servicii din alte aplicații."),
    ("Ce este JSON?", "JSON (JavaScript Object Notation) este un format ușor de schimb de date, care este ușor de citit și scris atât pentru oameni, cât și pentru mașini."),
    ("Ce este un server web?", "Un server web este un software care servește pagini web utilizatorilor la cerere prin intermediul unui browser."),
    ("Ce este un protocol HTTP?", "HTTP (HyperText Transfer Protocol) este protocolul folosit pentru a transfera pagini web de la un server la un client."),
    ("Ce este Python?", "Python este un limbaj de programare interpretat, de nivel înalt și de uz general."),
    ("Ce este un API REST?", "Un API REST (Representational State Transfer) este un tip de API care utilizează HTTP pentru a efectua operațiuni CRUD (Create, Read, Update, Delete) pe resurse."),
    ("Ce este un framework?", "Un framework este un cadru de lucru care oferă suportul necesar pentru dezvoltarea aplicațiilor software."),
    ("Ce este HTML?", "Este limbajul standard pentru crearea și structura paginilor web."),
    ("Ce este CSS?", "Descrie aspectul și formatarea unui document scris în HTML."),
    ("Ce este Python?", "Este un limbaj de programare utilizat pentru gestionarea și manipularea datelor."),
    ("Ce este SQL?", "Este un limbaj pentru gestionarea și manipularea bazelor de date relaționale."),
    ("Ce este Git?", "Este un sistem de control al versiunilor care descrie fișierele de cod sursă în timpul dezvoltării software."),
    ("Ce este JSON?", "Este un format de date ușor de citit și de scris utilizat pentru schimbul de date între un server și un client."),
    ("Ce este un framework?", "Este o structură sau un set de instrumente care facilitează dezvoltarea, testarea și implementarea aplicațiilor software.")
]


# Preprocesare text
def preprocess_text(text):
    text = text.lower()
    # Elimină caracterele speciale
    text = re.sub(r'[^\w\s]', '', text)
    return text

# Extrage întrebările și răspunsurile
questions = [preprocess_text(question) for question, _ in conversations]
responses = [preprocess_text(response) for _, response in conversations]

# Tokenizare și pad secvențe
tokenizer = Tokenizer()
tokenizer.fit_on_texts(questions + responses)

max_len = max([len(tokenizer.texts_to_sequences([question])[0]) for question in questions])
question_sequences_padded = pad_sequences(tokenizer.texts_to_sequences(questions), maxlen=max_len, padding='post')
response_sequences_padded = pad_sequences(tokenizer.texts_to_sequences(responses), maxlen=max_len, padding='post')
response_sequences_padded_one_hot = to_categorical(response_sequences_padded, num_classes=len(tokenizer.word_index) + 1)

# Definirea modelului
model = Sequential([
    # Strat de încorporare (embedding)
    
    Embedding(len(tokenizer.word_index) + 1, 128, input_length=max_len),
    Bidirectional(LSTM(256, return_sequences=True)),
    Dropout(0.2),
    LSTM(256, return_sequences=True),
    TimeDistributed(Dense(len(tokenizer.word_index) + 1, activation='softmax'))
    
])

# Compilarea modelului
model.compile(loss='categorical_crossentropy', optimizer='adam', metrics=['accuracy'])

# Antrenarea modelului
model.fit(question_sequences_padded, response_sequences_padded_one_hot, epochs=300, batch_size=32, validation_split=0.2)

@app.route('/chat', methods=['GET'])
def chat():
    user_input = request.args.get("message")
    
    if user_input is None:
        return jsonify({"response": "No message provided."}), 400
    
    response = get_bot_response(preprocess_text(user_input))
    
    # Răspunsul bot-ului
    return jsonify({"response": response}), 200

# Funcția pentru generarea răspunsului bot-ului
def get_bot_response(question):
    question_sequence = pad_sequences(tokenizer.texts_to_sequences([question]), maxlen=max_len, padding='post')
    response_sequences = [model.predict(question_sequence)[0] for _ in range(10)]
    response_sequence_padded = max(response_sequences, key=lambda seq: np.sum(seq))
    response_sequence = [tokenizer.index_word[idx] for idx in np.argmax(response_sequence_padded, axis=-1) if idx != 0]
    
    response = " ".join([word if word not in ['.', '?', '!'] else word for word in response_sequence])
    
    if len(response.split()) < 10:
        random_response = random.choice(responses)
        response += random_response.split('.')[0] + '.'
    
    return response.capitalize()

# Rularea serverului
if __name__ == '__main__':
    app.run(debug=True)
