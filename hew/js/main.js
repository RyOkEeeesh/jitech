const $ = e => document.querySelector(e);
const $A = e => document.querySelectorAll(e);


const fadein = () => {
  const FADEIN_SECOND = 0.14;
  const Target = $('main');
  Target.style.transition = `all ${FADEIN_SECOND}s`;
  Target.style.opacity = 1;
};

const fadeout = () => 
  new Promise(resolve => {
    const FADEOUT_SOCOND = 0.1;
    const target = $('main');
    target.style.transition = `all ${FADEOUT_SOCOND}s`;
    target.style.opacity = '0';
    setTimeout(() => resolve(), FADEOUT_SOCOND * 1000);
  });

const getPageByXHR = path => 
  new Promise((resolve,reject) => {
    const xhr = new XMLHttpRequest();
    xhr.timeout = 5000;
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) resolve(xhr.responseText);
        reject(new Error(`Request failed with status ${xhr.status}`));
      }
    };
    xhr.ontimeout = () => reject(new Error(`Request failed with timeout ${xhr.timeout}ms`));
    xhr.open('GET', path);
    xhr.send();
  });

const rewrightPage = elem => {
  const parser = new DOMParser();
  const newDoc = parser.parseFromString(elem, "text/html");
  $('main').innerHTML = newDoc.querySelector('main').innerHTML;
};

const setups = () => {
  const urlParams = new URLSearchParams(window.location.search);
  if ( !urlParams.get('no') ) {
    document.onscroll = () => {
      try {
        const h1Rect = $('.index__h1').getBoundingClientRect();
        const contentRect = $('.post-content').getBoundingClientRect();
        $('.index__h1').style.opacity = h1Rect.bottom > contentRect.top ? '0' : '1' ;
      } catch {
        return;
      }
    };
  }

  $A('.has-data').forEach(elem => {
    elem.onclick = e => {
      let href = elem.getAttribute('data-href');
      e.preventDefault();
      if ( href.match(/^\.\.\/(\.\.\/)?$/) ) href = './';
      if ( !href.match(/^[0-9]{1,16}$|^\.\/$/) ) {
        location.href = './';
        return;
      };
      const param = href.match(/^\d+$/) ? `?no=${href}` : href;
      Promise.all([fadeout(), getPageByXHR(param)]).then(response => {
        history.pushState(null, null, location.href);
        history.replaceState(null, null, param);
        rewrightPage(response);
        fadein();
        setups();
      }).catch(error => {
        console.error(error);
        window.location.href = './';
      });
    };
  });
};

const main = () => {
  setups();
  window.onpopstate = () => {
    Promise.all([fadeout(), getPageByXHR(location.href)]).then(response => {
      rewrightPage(response);
      fadein();
      setups();
    }).catch(error => {
      console.error(error);
      window.location.href = './';
    });
  };
};

main();