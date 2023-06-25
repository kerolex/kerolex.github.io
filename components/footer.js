const footerTemplate = document.createElement('template');

footerTemplate.innerHTML = `
  <footer class="site-footer">
		<div class="footer-col social-col" style="text-align:center;">  
				<a href="https://twitter.com/aXompi"><i class="fab fa-fw fa-twitter" aria-hidden="true" style="padding-right: 1.5em;"></i></a>
				<a href="https://www.linkedin.com/in/alessioxompero/?locale=en_US"><i class="fab fa-fw fa-linkedin" aria-hidden="true" style="padding-right: 1.5em;"></i></a>
				<a href="https://github.com/kerolex/"><i class="fab fa-fw fa-github" aria-hidden="true" style="padding-right: 1.5em;"></i></a>
				<a href="https://orcid.org/0000-0002-8227-8529"><i class="ai ai-orcid ai-fw" aria-hidden="true" style="padding-right: 1.5em;"></i></a>
				<a href="https://scholar.google.com/citations?user=r7jxqOAAAAAJ&hl=en&oi=ao"><i class="ai ai-fw ai-google-scholar" aria-hidden="true" style="padding-right: 1.5em;"></i></a>
			</div>
		<p>
			Copyright © 2015-2023 - Alessio Xompero
		</p>
	</footer>
`;

class Footer extends HTMLElement {
  constructor() {
    super();
  }

  connectedCallback() {
    const shadowRoot = this.attachShadow({ mode: 'closed' });

    shadowRoot.appendChild(footerTemplate.content);
  }
}

customElements.define('footer-component', Footer);

