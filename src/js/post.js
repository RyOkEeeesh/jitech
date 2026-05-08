const $ = e => document.querySelector(e);
const title = $('#title');
const desc = $('#description');
const dfThumb = $('#df-thumb');
const thumbnailInput = $('#thumbnail');
const fileInput = $('#fileInput');
const submit = $('#submitBtn');
const reset = $('#resetBtn');
const thumbDrop = $('.thumb-drop');
const thumbDragLeave = $('.thumb-drag-leave');
const thumbDragEnter = $('.thumb-drag-enter');
const fileDrop = $('.file-drop');
const fileDragLeave = $('.file-drag-leave');
const fileDragEnter = $('.file-drag-enter');
const prevContent = $('.prev-content');
const prevImg = $('.preview-img');
const descContent = $('.desc-content');
const descTxt = $('.desc-txt');
const titleWrap = $('.title-wrap');
const prevTitle = $('.post-title');
const thumbArea = $('.thumb-area');
const spacer = $('.spacer');
const thumbList = $('.thumb-list');
const fileList = $('.file-list');
const postTime = $('.post-time');
const dateTime = new Date();
const date = new Date(dateTime.getFullYear(), dateTime.getMonth(), dateTime.getDate());

const formatDate = (date, sep='') => {
  const yyyy = date.getFullYear();
  const mm = ('00' + (date.getMonth() + 1)).slice(-2);
  const dd = ('00' + date.getDate()).slice(-2);
  return `${yyyy}${sep}${mm}${sep}${dd}`;
};
postTime.innerHTML = formatDate(date, "/");

let isTitleEmpty = true;
let isThumbnailInputEmpty = true;
let isfileInputEmpty = true;
const checkElem = () => { return !isTitleEmpty && ( !isThumbnailInputEmpty || dfThumb.checked ) && !isfileInputEmpty };

const formData = new FormData();
const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];

const submitOnOff = () => {
  if ( checkElem() ) {
    submit.classList.add('on');
    submit.classList.remove('off');
    $('.btn-content').style.cursor = 'pointer';
    return;
  } else {
    submit.classList.add('off');
    submit.classList.remove('on');
    $('.btn-content').style.cursor = 'not-allowed';
    return;
  }
};

const resetAll = () => {
  title.value = '';
  desc.value = '';
  thumbnailInput.value = '';
  thumbList.innerHTML = '';
  fileInput.value = '';
  fileList.innerHTML = '';
  prevImg.src = ''
  prevImg.style.display = 'none';
  descTxt.innerHTML = '';
  prevTitle.innerHTML = '';
  thumbArea.classList.add('thumb-open');
  spacer.classList.remove('thumb-open');
  isTitleEmpty = true;
  isThumbnailInputEmpty = true;
  isfileInputEmpty = true;
  dfThumb.checked = false;
  submitOnOff();
};

const result = $('.result');

const resultMsg = msg => {
  const tx = document.createElement('p');
  tx.innerText = msg;
  tx.classList = 'result-msg';
  result.appendChild(tx);
};

const displayErrMsg = msg => {
  const tx = document.createElement('p');
  tx.innerText = msg;
  tx.classList = 'err-msg';
  result.appendChild(tx);
};

const displayAlertMsg = msg => alert(msg);

title.oninput = () => {
  if ( title.value && title.value.match(/\S/g) ) {
    isTitleEmpty = false;
    prevTitle.innerText = title.value;
    formData.set( 'title', title.value );
  } else {
    isTitleEmpty = true;
    prevTitle.innerText = '';
    formData.set( 'title', '' );
  }
  submitOnOff();
};

desc.oninput = () => {
  if ( desc.value && desc.value.match(/\S/g) ) {
    formData.set( 'description', desc.value );
    descContent.style.display = 'block';
    descTxt.innerText = desc.value;
  } else {
    formData.set( 'description', '' );
    descContent.style.display = 'none';
    descTxt.innerText = '';
  }
};

const processThumb = () => {
  const thumbnail = thumbnailInput.files[0];
  thumbList.innerHTML = '';
  if (thumbnail) {
    isThumbnailInputEmpty = false;
    if (!validImageTypes.includes(thumbnail.type)) {
      displayAlertMsg('写真ファイルを選択してください\n(jpeg, png, gif)');
      thumbnailInput.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      prevImg.src = e.target.result;
      prevImg.style.display = 'block';
    }
    reader.readAsDataURL(thumbnail);

    const fileName = document.createElement('p');
    fileName.innerText = thumbnail.name;
    fileName.classList = 'thumb-name';
    thumbList.appendChild(fileName);

  } else {
    isThumbnailInputEmpty = true ;
    prevImg.style.display = 'none';
    prevImg.src = '';
    thumbList.innerHTML = '';
  }
  submitOnOff();
  formData.set( 'thumbnail', thumbnail );
};

dfThumb.onchange = () => {
  if ( dfThumb.checked ) {
    thumbArea.classList.remove('thumb-open');
    spacer.classList.add('thumb-open');
    formData.set( 'thumbnail', '' );
    prevImg.style.display = 'block';
    prevImg.src = './thumbnails/post.png';
  } else {
    thumbArea.classList.add('thumb-open');
    spacer.classList.remove('thumb-open');
    processThumb();
  }
  submitOnOff();
};

thumbnailInput.onchange = processThumb;

fileInput.onchange = () => {
  formData.delete('files[]');
  const files = fileInput.files;
  isfileInputEmpty = files ? false : true ;

  fileList.innerHTML = '';
  submitOnOff();
  for (let i = 0; i < files.length; i++) {
    formData.append('files[]', files[i], files[i].webkitRelativePath);
    const fileName = document.createElement('p');
    fileName.innerText = files[i].name;
    fileName.classList = 'file-name';
    fileList.appendChild(fileName);
  }
};

const dragMovement = (hide, show) => {
  hide.classList.remove('show-drop-area');
  hide.classList.add('dsp-none');
  show.classList.remove('dsp-none');
  show.classList.add('show-drop-area');
};

let dragCounter = 0;

thumbDrop.ondragenter = e => {
  e.preventDefault();
  dragCounter++;
  dragMovement(thumbDragLeave, thumbDragEnter);
};

thumbDrop.ondragleave = e => {
  e.preventDefault();
  dragCounter--;
  if (dragCounter === 0) dragMovement(thumbDragEnter, thumbDragLeave);
};

thumbDrop.ondragover = e => {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'copy';
};

thumbDrop.ondrop = e => {
  e.preventDefault();
  dragCounter = 0;
  dragMovement(thumbDragEnter, thumbDragLeave);

  const handleFileDrop = new Promise((resolve, reject) => {
    const file = e.dataTransfer.files;
    if (file && file.length === 1) {
      resolve(file);
    } else {
      reject('サムネイルに使用できるファイルは1つまでです');
    }
  });

  handleFileDrop
    .then(file => {
      thumbnailInput.files = file;
      thumbnailInput.dispatchEvent(new Event('change'));
    })
    .catch(error => {
      displayAlertMsg(`Error:${error}`);
      thumbnailInput.value = '';
    });
};

fileDrop.ondragenter = e => {
  e.preventDefault();
  dragCounter++;
  dragMovement(fileDragLeave, fileDragEnter);
}

fileDrop.ondragleave = e => {
  e.preventDefault();
  dragCounter--;
  if (dragCounter === 0) dragMovement(fileDragEnter, fileDragLeave);
}

fileDrop.ondragover = e => {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'copy';
}

fileDrop.ondrop = async (event) => {
  event.preventDefault();
  dragCounter = 0;
  dragMovement(fileDragEnter, fileDragLeave);

  const handleFiles = (files) => {
    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
    fileInput.dispatchEvent(new Event('change'));
  };

  const items = event.dataTransfer.items;
  const entries = Array.from(items).map(item => item.webkitGetAsEntry());

  const files = entries.filter(entry => entry && entry.isFile);
  const directories = entries.filter(entry => entry && entry.isDirectory);

  if (directories.length === 1 && files.length === 0) {
    const files = [];

    const searchFile = async (entry) => {
      if (entry.isFile) {
        const file = await new Promise(resolve => {
          entry.file(file => {
            Object.defineProperty(file, "webkitRelativePath", {
              value: entry.fullPath.slice(1),
            });
            resolve(file);
          });
        });
        files.push(file);
      } else if (entry.isDirectory) {
        const dirReader = entry.createReader();
        const getEntries = () => new Promise(resolve => dirReader.readEntries(resolve));
        let entries;
        do {
          entries = await getEntries();
          for (const entry of entries) {
            await searchFile(entry);
          }
        } while (entries.length > 0);
      }
    };

    await searchFile(directories[0]);
    handleFiles(files);
  } else {
    displayAlertMsg('投稿ファイルの入ったフォルダを1つドラッグアンドドロップしてください');
    fileInput.value = '';
  }
};

resetAll();

reset.onclick = resetAll;

const loading = () => {
  document.body.style.cursor = 'wait';
  $('.post__main').style.pointerEvents = 'none';
};

const defaultDocument = () => {
  document.body.style.cursor = 'default';
  $('.post__main').style.pointerEvents = 'auto';
};

let alreadySubmit = false;

submit.onclick = e => {
  e.preventDefault();
  if ( alreadySubmit ) return;
  if ( !checkElem() ) return;
  alreadySubmit = true;
  loading();
  const url = './upload.php';
  const xhr = new XMLHttpRequest();

  xhr.open('POST', url, true);
  xhr.responseType = 'text';
  xhr.timeout = 10000;


  xhr.onload = () => {
    if (xhr.status != 200) displayErrMsg(`Error ${xhr.status}: ${xhr.statusText}`);
    else resultMsg(xhr.response);
  };

  xhr.onerror = e => displayErrMsg(`Error : ${e}`);

  xhr.onreadystatechange = () => {
    if ( xhr.readyState === 4 ) defaultDocument();
  };

  xhr.onloadend = () => {
    resetAll();
    alreadySubmit = false;
  };

  xhr.send(formData);

};