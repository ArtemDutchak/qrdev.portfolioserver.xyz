<section class="add-company-page page-content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-12">
        <h2>Добаить новую компанию</h2>
      </div>
    </div>

    <form class="row add-company-form" id="add_company_form" enctype="multipart/form-data">
      <div class="col-xl-6 col-lg-6 form-block">

        <div class="form-group">
          <h3>Загрузите логотип компании</h3>
          <p class="logo-requirements">Требования к логотипу: Мин.&nbsp;150px&nbsp;x&nbsp;50px&nbsp;Макс.&nbsp;1000&nbsp;px&nbsp;x&nbsp;1000&nbsp;px</p>
          <div class="dropzone-wrapper">
            <span class="remove-preview">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" fill="white" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.16992 14.8299L14.8299 9.16992" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.8299 14.8299L9.16992 9.16992" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>                
            </span>
            <input type="file" name="img_logo" class="dropzone" accept=".jpg, .jpeg, .png">
            <div class="dropzone-desc">
              <svg class="icon-dropzone" width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M38.756 46.6665C41.8827 46.6899 44.8927 45.5232 47.2027 43.4232C54.8327 36.7499 50.7494 23.3565 40.6927 22.0965C37.0994 0.303215 5.66937 8.56321 13.1127 29.3065" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16.9859 30.2631C15.7492 29.6331 14.3725 29.3064 12.9959 29.3298C2.12253 30.0998 2.14586 45.9198 12.9959 46.6898" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M36.9141 23.0768C38.1274 22.4701 39.4341 22.1435 40.7874 22.1201" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M30.263 46.6665H20.9297" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M25.5957 51.3333V42" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>                
              <p class="text-dropzone">Перетащите и вставьте или выберите и загрузите файл</p>
              <!-- error messages -->
              <p class="very-big-img error-message d-none">Слишком большое изображение</p>
              <p class="very-small-img error-message d-none">Слишком маленькое изображение</p>
              <!-- <p class="cannot-be-empty d-none">Выберите логотип вашей компании</p> -->
            </div>
          </div>
        </div>

        <label class="type-text" for="company_name">Название компании*</label>
        <div class="company-name-group input-group">
          <input class="company-name input-text" type="text" name="company_name" id="company_name" placeholder="Введите название своей компании" autocomplete="off">
          <span class="error-message d-none">Обязательно укажите название компании.</span>
        </div>
        
        <div class="need-phone-group">
          <div class="text">
            <span>Номер телефона клиента при отправке отзыва</span>
            <p>Сделать обязательным, если оценка отзыва меньше или равна </p>
          </div>
          <div class="quantity">
            <!-- .quantity-button-star (!) with this class the logic changes -->
            <div class="quantity-button quantity-button-star  quantity-down">
              <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1H13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </div>
            <input class="d-none" type="number" name="need_phone_number" min="1" max="5" step="1" value="1">
            <span class="quantity-number">1</span>
            <!-- .quantity-button-star (!) with this class the logic changes -->
            <div class="quantity-button quantity-button-star quantity-up">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 7H13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M7 13V1" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>                  
            </div>
          </div>

          <input class="need-phone d-none" type="checkbox" name="need_phone" id="need_phone">
          <label for="need_phone" title="off - on"><span class="on-off"></span></label>
        </div>

        <div class="duplicate-review-group">
          <div class="text">
            <span>Продублировать положительный отзыв на публичные сервисы</span>
            <p>Минимальная оценка для предложения продублировать отзыв</p>
          </div>
          <div class="quantity">
            <!-- .quantity-button-star (!) with this class the logic changes -->
            <div class="quantity-button quantity-button-star  quantity-down">
              <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1H13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>                  
            </div>
            <input class="d-none" type="number" name="need_phone_number" min="1" max="5" step="1" value="1">
            <span class="quantity-number">1</span>
            <!-- .quantity-button-star (!) with this class the logic changes -->
            <div class="quantity-button quantity-button-star quantity-up">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 7H13" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M7 13V1" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>                  
            </div>
          </div>
  
          <input class="duplicate-review d-none" type="checkbox" name="duplicate_review" id="duplicate_review">
          <label for="duplicate_review" title="off - on"><span class="on-off"></span></label>
        </div>
        <h3>
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.033 4.8501C14.5163 4.8501 10.033 9.33343 10.033 14.8501V19.6668C10.033 20.6834 9.59966 22.2334 9.08299 23.1001L7.16633 26.2834C5.98299 28.2501 6.79966 30.4334 8.96632 31.1668C16.1497 33.5668 23.8997 33.5668 31.083 31.1668C33.0997 30.5001 33.983 28.1168 32.883 26.2834L30.9663 23.1001C30.4663 22.2334 30.033 20.6834 30.033 19.6668V14.8501C30.033 9.3501 25.533 4.8501 20.033 4.8501Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
            <path d="M23.1159 5.3334C22.5992 5.1834 22.0659 5.06673 21.5159 5.00006C19.9159 4.80006 18.3826 4.91673 16.9492 5.3334C17.4326 4.10006 18.6326 3.2334 20.0326 3.2334C21.4326 3.2334 22.6325 4.10006 23.1159 5.3334Z" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M25.0332 31.7666C25.0332 34.5166 22.7832 36.7666 20.0332 36.7666C18.6665 36.7666 17.3999 36.1999 16.4999 35.2999C15.5999 34.3999 15.0332 33.1333 15.0332 31.7666" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10"/>
          </svg>
          <span class="h3">Уведомления об отзывах</span>
        </h3>
        <div class="label-wrap">
          <label class="type-text" for="telegram_phone">Введите номер телефона Telegram</label>
          <a class="info-link" href="/" target="_blank">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.4779 13.8997V8.99961M10.3088 6.02092V5.70444M10.0647 2.00195C13.6961 2.09406 20.1053 5.82085 17.3162 13.4845C13.8298 23.0641 1.13978 15.1904 2.0462 10.1381C2.95263 5.08575 6.43329 1.90984 10.0647 2.00195Z" stroke="#3B5498" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span class="link-text">Подробнее как подключить</span>
          </a>
        </div>
        <div class="telegram-group input-group">
          <input class="check-telegram-number d-none" type="checkbox" name="check_telegram_phone" id="check_telegram_phone">
          <input type="tel" class="telegram-number" name="telegram_phone" id="telegram_phone" placeholder="+380" autocomplete="off">
          <label for="check_telegram_phone" title="off - on"><span class="on-off"></span></label>
          <span class="error-message d-none">Введите телефон Telegram.</span>
        </div>
        <label class="type-text" for="email">E-mail</label>
        <div class="email-group input-group">
          <input class="check-email d-none" type="checkbox" name="check_email" id="check_email">
          <input type="email" class="email" name="email" id="email" placeholder="Введите E-mail" autocomplete="off">
          <label for="check_email" title="off - on"><span class="on-off"></span></label>
          <span class="error-message d-none">Введите правильный E-mail.</span>
        </div>
      </div>

      <div class="col-xl-6 col-lg-6">
        <label class="type-text">Текстовое поле</label>
        <div class="text-field-group input-group">
          <input class="check-text-field d-none" type="checkbox" name="check_text_field" id="check_text_field">
          <textarea class="text-field" name="text_field" id="text_field" placeholder="Напишите ваш текст" autocomplete="off">Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo dolores ut laborum porro, corrupti excepturi possimus perspiciatis cum? Ducimus, officia.</textarea>
          <label for="check_text_field" title="off - on"><span class="on-off"></span></label>
          <span class="error-message d-none">Сообщение об ошибке</span>
        </div>
        <label class="type-text">Текстовое поле</label>
        <div class="text-field-group input-group">
          <input class="check-text-field d-none" type="checkbox" name="check_small_text_field" id="check_small_text_field">
          <textarea class="text-field" name="small_text_field" id="small_text_field" placeholder="Напишите ваш текст" autocomplete="off">Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo dolores ut laborum porro, corrupti excepturi possimus perspiciatis cum? Ducimus, officia.</textarea>
          <label for="check_small_text_field" title="off - on"><span class="on-off"></span></label>
          <span class="error-message d-none">Сообщение об ошибке</span>
        </div>
        <h3>Рекомендация клиенту</h3>
        <p class="recomend-text">Продублировать положительный отзыв на публичные сервисы.</p>
        <p class="recomend-text">Также рекомендуем брать ссылки из мобильных приложений.</p>
        <label class="type-text" for="google">Google</label>
        <div class="google-group input-group">
          <input class="check-google d-none" type="checkbox" name="check_google" id="check_google">
          <input type="text" class="google" name="google" id="google" placeholder="Укажите ссылку на профиль" autocomplete="off">
          <label for="check_google" title="off - on"><span class="on-off"></span></label>
          <span class="error-message d-none">Сообщение об ошибке</span>
        </div>
        <label class="type-text" for="facebook">Facebook</label>
        <div class="facebook-group input-group">
          <input class="check-facebook d-none" type="checkbox" name="check_facebook" id="check_facebook">
          <input type="text" class="facebook" name="facebook" id="facebook" placeholder="Укажите ссылку на профиль" autocomplete="off">
          <label for="check_facebook" title="off - on"><span class="on-off"></span></label>
          <span class="error-message d-none">Сообщение об ошибке</span>
        </div>
        <label class="type-text" for="profile_link">Ссылка на другой источник</label>
        <div class="profile-link-group input-group">
          <input class="check-profile-link d-none" type="checkbox" name="check_profile_link" id="check_profile_link">
          <input type="text" class="profile-name" name="profile_name" id="profile_name" placeholder="Название ресурса" autocomplete="off">
          <label for="check_profile_link" title="off - on"><span class="on-off"></span></label>
          <input type="text" class="profile-link" name="profile_link" id="profile_link" placeholder="Укажите ссылку на профиль" autocomplete="off">
          <span class="error-message d-none">Сообщение об ошибке</span>
        </div>
          
        <input class="btn-rate-pay" type="submit" value="Сохранить настройки">
      </div>
    </form>
  </div>
</section>