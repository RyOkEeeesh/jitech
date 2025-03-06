const urlParams = new URLSearchParams(window.location.search);
const mode = urlParams.get('no') ? 'edit' : 'chart';
const $ = e => document.querySelector(e);

const editProcess = () => {
  const formData = new FormData();
  const urlParams = new URLSearchParams(window.location.search);
  const no =  urlParams.get('no');
  
  formData.set('no', no);
  
  const title = $('.edit__input');
  const desc = $('.edit__textarea');
  const editTitle = $('.edit-title');
  const descContent = $('.desc-content');
  const descTxt = $('.desc-txt');
  const shInput = $('.sh__input');

  const canEdit = () => Boolean( title.value && title.value.match(/\S/g) );
  const editBtn = $('.edit-btn');

  const defaultInfo = {
    'title' : title.value,
    'desc' : desc.value,
    'hidden' : shInput.checked ? '1' : '0'
  }

  Object.keys(defaultInfo).forEach(key => formData.set(key, defaultInfo[key]));

  const isSameAsDefault = () => Boolean( defaultInfo.title == title.value && defaultInfo.desc == desc.value && defaultInfo.hidden == (shInput.checked ? '1' : '0') );

  const editOnOff = () => {
    if ( canEdit() && !isSameAsDefault() ) {
      editBtn.classList.add('on');
      editBtn.classList.remove('off');
    } else {
      editBtn.classList.add('off');
      editBtn.classList.remove('on');
    }
  }

  const sendFormdataByXHR = () => 
    new Promise(resolve => {
      const url = './edit.php';
      const xhr = new XMLHttpRequest();
      xhr.open('POST', url, true);
      xhr.responseType = 'text';
      xhr.timeout = 10000;

      xhr.onload = () => resolve(xhr.response);

      xhr.send(formData);
    });


  editOnOff();

  title.oninput = () => {
    if (title.value && title.value.match(/\S/g)) {
      editTitle.innerText = title.value;
      formData.set('title', title.value);
    } else {
      editTitle.innerText = '';
      formData.set('title', '');
    }
    editOnOff();
  };

  desc.oninput = () => {
    if (desc.value && desc.value.match(/\S/g)) {
      formData.set('desc', desc.value);
      descContent.style.display = 'block';
      descTxt.innerText = desc.value;
    } else {
      formData.set('desc', '');
      descContent.style.display = 'none';
      descTxt.innerText = '';
    }
    editOnOff();
  };

  shInput.onchange = e => {
    formData.set('hidden', e.target.checked ? '1' : '0');
    editOnOff();
  };

  let alreadyEdit = false;

  editBtn.onclick = e => {
    e.preventDefault();
    editOnOff();
    if ( alreadyEdit ) return;
    if ( !canEdit() ) return;
    if ( isSameAsDefault() ) return;

    alreadyEdit = true;

    sendFormdataByXHR()
    .then(response => alert(response))
    .then(() => alreadyEdit = false);

  };

  $('.del-btn').onclick = () => {
    const accept = new Promise(resolve => resolve(confirm('この投稿を削除しますか')));
    accept.then(result => {
      if ( result ) {
        Object.keys(defaultInfo).forEach(key => formData.delete(key));
        formData.set('delete', true);
        sendFormdataByXHR().then(response => console.log(response));
      }
    });
  }

  $('.reset-btn').onclick = () => {
    title.value = defaultInfo.title;
    title.dispatchEvent(new Event('input'));
    desc.value = defaultInfo.desc;
    desc.dispatchEvent(new Event('input'));
    shInput.checked = Boolean(parseInt(defaultInfo.hidden));
    shInput.dispatchEvent(new Event('change'));
  }

};

let chartInstance = null;

const chartProcess = () => {
  const url = './accessCount.php';
  const selectDate = document.querySelector('.admin-date').value;
  const formData = new FormData();
  formData.set('date', selectDate);

  const hourlyAccessCounts = new Promise(resolve => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.responseType = 'json';
    xhr.timeout = 10000;
    xhr.onload = () => resolve(xhr.response);
    xhr.send(formData);
  });

  hourlyAccessCounts
    .then(data => {
      if (data) {
        const ctx = document.getElementById('accessChart').getContext('2d');
        const labels = Array.from({length: 24}, (_, i) => i + ':00');

        const chartData = {
          labels: labels,
          datasets: [{
            label: 'アクセス数',
            data: data,
            backgroundColor: 'rgba(75, 192, 192, 0.8)'
          }]
        };

        const config = {
          type: 'bar',
          data: chartData,
          options: {
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  color : '#adadad',
                  stepSize: 1,
                },
                grid: {
                  color: 'rgba(173, 173, 173, 0.4)'
                }
              },
              x: {
                ticks: {
                  color: '#adadad'
                },
                grid: {
                  color: 'rgba(173, 173, 173 , 0.2)'
                }
              }
            }
          }
        };

        if (chartInstance) chartInstance.destroy();

        chartInstance = new Chart(ctx, config);
      } else {
        console.error('データの取得に失敗しました。');
      }
    })
    .catch(error => {
      console.error('エラーが発生しました:', error);
    });

  $('.admin-date').onchange = chartProcess;

};



const main = () => {
  if (mode === 'edit') {
    // edit function
    editProcess();
    return;
  }
  if (mode === 'chart') {
    // chart function
    chartProcess();
    return;
  }
};

main();