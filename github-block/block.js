const { registerBlockType } = wp.blocks; // A function for instantiating blocks
const el = wp.element.createElement; // Used to create a React element
const { TextControl } = wp.components; // Provides access to text input
const __ = wp.i18n.__; // JS function for translatable strings

registerBlockType( 'dainemawer/github' , {

    title: __( 'Github Embed' ),
    description: __( 'Uses the Github Public API to embed beautiful Medium.com inspired repository embeds' ),
    keywords: [__( 'embed' ), __( 'github' ), __( 'widget' )],
    html: false, // Important if you dont want people to edit the HTML of your block
    icon: 'id',
    category: 'widgets',
    attributes: {
        url: {
            type: 'string' // A bit like strict typing, attributes need to have a type associated with it
        }
    },

    edit: ( props ) => {

        /*
        * 1) Set up TextControl to allow users to input a value, in this case a Github URL
        * 2) Using fetch(), grab data from Github and build widget in the editor
        * 3) Push el() to the repo array to create a new element in the editor.
        */

        // Grab our value from props. If its exists, give it to us. 
        // If not just give us an empty string so we dont error out.
        const url = props.attributes.url || ''; 

        // Regex to check a string is a URL ¯\_(ツ)_/¯
        const expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;

        // Run our expression through the native JS Regex class
        const regex = new RegExp( expression );

        // Focus should always be truthy
        const focus = props.focus; 

        // We're going to array.push() multiple elements to this array.
        const repo = [];

        // If the element isnt focused and the url is empty setup our control for the user
        if( !! focus || ! url.length ) {

            const controlConfig = {
                value: url,
                label: __( 'Github Repository URL' ),
                help: __( 'Enter a valid repository url, e.g https://github.com/WordPress/WordPress' ),
                placeholder: __( 'Enter a Github Repository URL' ),
                focus: focus, 
                onFocus: props.setFocus,
                onChange: ( val ) => { // Handles user input
                    props.setAttributes({
                        url: val
                    });
                }
            }

            // While we're 'editing' setup a TextControl
            repo.push( el( TextControl, controlConfig ) ); 

        }

        // Once we have a URL and we know it is a URL
        if( url.length > 0 && url.match( regex ) ) {

            // Set some dynamic ID's and Classes
            const id = 'github-embed-' + props.id;
            const className = 'github-embed-wrapper';
            
            // Build Github API Endpoint
            const apiURL = 'https://api.github.com/repos/';

            // As the API endpoint is different, we replace here before runnnig fetch()
            const endpoint = url.replace('https://github.com/', '');

            fetch( apiURL + endpoint )

            // Convert response to valid json
            .then( ( response ) => response.json() )
            .then( ( response ) => {
                
                /* We target the div that is created by Gutenbergs el() / createElement;
                * Because we're running the Promise API, fetch runs before Gutenberg has made the element available via createElement()
                * so we need to grab the div during a successful promise function callback.
                */

                let div = document.getElementById( id );

                // Setup markup and data to be displayed in the editor
                div.innerHTML = '<div class="repo-description"><a href="'+ response.html_url +'" title="'+ response.name +'" target="_blank" rel="noopener noferrer">'+ response.full_name +'</a><p>'+ response.description +'</p></div>';
				div.innerHTML += '<a href="'+ url +'" class="avatar_img" style="background-image: url('+ response.owner.avatar_url +')" target="_blank" rel="noopener noferrer"></a>';

            }).catch( ( error ) => {

                console.log(error.response);
                // Handle errors here, we need to grab this div again because of the Promise API.
                let div = document.getElementById( id );
                div.innerHTML = '<div class="repo-description">'+ __( 'Bummer, looks like there was an error retrieving data, have you checked that the Github URL above is correct?' ) +'</div>';

            }); 

            // Push a div element with dynamic ID and class attributes
            repo.push( el( 'div', { id: id, class: className } ) );

        }

        // Send back the entire array to the API
        return repo;
        
    },

    save: ( ) => {

        // We return null here as we want the saved content to be handled by PHP.
        return null;
    }
    
});