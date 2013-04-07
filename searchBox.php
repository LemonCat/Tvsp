<form id="quick-search" action="./searchResults.php" method="get" >
    <p>
	<label for="qsearch">Search:</label>
	
	<input class="tbox" id="qsearch" type="text" name="qsearch" onfocus="if(this.value == 'Rechercher'){this.value = '';}" type="text" onblur="if(this.value == ''){this.value='Rechercher';}" id="name" value="Rechercher" />
        <input class="btn" alt="Search" type="image" name="searchsubmit" title="Search" src="images/search.gif" />
        
    </p>
</form>