{% if bDeleteEntity|Exists %}
	{% if bDeleteEntity %}
          <h4>Suppression réussie!</h4>
          <p>Votre enregistrement à correctement été supprimé</p>
	{% else %}
	      <h4>Une erreur est survenue!</h4>
	      <p>Impossible de supprimer votre enregistrement...</p>
	{% endif %}
{% endif %}
