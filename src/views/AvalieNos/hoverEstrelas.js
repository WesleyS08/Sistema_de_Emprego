function OnUmaEstrela(){
    umaEstrela.style.transform = 'scale(1.2)';
}

function OffUmaEstrela(){
    umaEstrela.style = "initial";
}

// ================================================

function OnDuasEstrelas(){
    umaEstrela.style.transform = 'scale(1.2)';
    duasEstrelas.style.transform = 'scale(1.2)';
}

function OffDuasEstrelas(){
    umaEstrela.style = "initial";
    duasEstrelas.style = "initial";
}

// ================================================

function OnTresEstrelas(){
    umaEstrela.style.transform = 'scale(1.2)';
    duasEstrelas.style.transform = 'scale(1.2)';
    tresEstrelas.style.transform = 'scale(1.2)';
}

function OffTresEstrelas(){
    umaEstrela.style = "initial";
    duasEstrelas.style = "initial";
    tresEstrelas.style = "initial";
}

// ================================================

function OnQuatroEstrelas(){
    umaEstrela.style.transform = 'scale(1.2)';
    duasEstrelas.style.transform = 'scale(1.2)';
    tresEstrelas.style.transform = 'scale(1.2)';    
    quatroEstrelas.style.transform = 'scale(1.2)';
}

function OffQuatroEstrelas(){
    umaEstrela.style = "initial";
    duasEstrelas.style = "initial";
    tresEstrelas.style = "initial";
    quatroEstrelas.style = "initial";
}

// ================================================

function OnCincoEstrelas(){
    umaEstrela.style.transform = 'scale(1.2)';
    duasEstrelas.style.transform = 'scale(1.2)';
    tresEstrelas.style.transform = 'scale(1.2)';    
    quatroEstrelas.style.transform = 'scale(1.2)';
    cincoEstrelas.style.transform = 'scale(1.2)';
}

function OffCincoEstrelas(){
    umaEstrela.style = "initial";
    duasEstrelas.style = "initial";
    tresEstrelas.style = "initial";
    quatroEstrelas.style = "initial";
    cincoEstrelas.style = "initial";
}

// ================================================

umaEstrela.addEventListener("mouseover", OnUmaEstrela);
umaEstrela.addEventListener("mouseout", OffUmaEstrela);

duasEstrelas.addEventListener("mouseover", OnDuasEstrelas);
duasEstrelas.addEventListener("mouseout", OffDuasEstrelas);

tresEstrelas.addEventListener("mouseover", OnTresEstrelas);
tresEstrelas.addEventListener("mouseout", OffTresEstrelas);

quatroEstrelas.addEventListener("mouseover", OnQuatroEstrelas);
quatroEstrelas.addEventListener("mouseout", OffQuatroEstrelas);

cincoEstrelas.addEventListener("mouseover", OnCincoEstrelas);
cincoEstrelas.addEventListener("mouseout", OffCincoEstrelas);