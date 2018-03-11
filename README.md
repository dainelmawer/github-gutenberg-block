# Github Embed Block for Gutenberg
A block for Gutenberg that adds a nicely formatted Github embed to post content in any WordPress theme.

## Installation

```git clone https://github.com/dainemawer/github-gutenberg-block.git```

- `cd` into the plugin folder, locate the webpack.config.js file

- In that directory run `npm run dev` to create a compiled `block.build.js`

- Make sure you have Gutenberg installed: https://wordpress.org/plugins/gutenberg/

- Log in to WordPress and visit Plugins -> All Plugins

- Activate "Github Embed Block"

- Navigate to Posts or Pages -> Add New or edit an already saved post / page.

- Click on the + icon in the Gutenberg editor and visit Widgets -> Github Embed

- Enjoy nicely styled, Medium inspired Github embeds!

## Usage

- The block uses the Public Github API, which means you only have 50 requests per hour. If you want to use the Oauth version of the Gihub API, which has to be tied to a Github account (5000 requests per hour) please create an issue on the repository. I am currently developing the Oauth version of the plugin.

## Demonstration

### From the Gutenberg Editor

![A demonstration of the Github Embed block for Gutenberg ](https://dha4w82d62smt.cloudfront.net/items/2B3e3u1u1Z2a073s3b1P/Screen%20Recording%202018-03-11%20at%2010.41%20AM.gif)

### From the Front-end

## License
GPL 2.0

## Support my work
The plugin is free to use, but I develop in my spare time, if you're happy with the development of the plugin, or want to see new features, please post an issue and support my work by making a donation: https://www.paypal.me/dainemawer
