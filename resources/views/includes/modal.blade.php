<div class="modal fade" id="message-modal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="alert alert-danger" style="display:none"></p>
                <textarea maxlength="1000" rows="6" required class="form-control" placeholder="Введите текст сообщения" id="message"></textarea>
                <input type="hidden" id="advert_id" value="" />
                @csrf
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success modal-message-button">Отправить</button>
            </div>
        </div>
    </div>
</div>
