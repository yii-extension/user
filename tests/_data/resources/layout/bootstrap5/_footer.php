<div class = 'd-flex flex-fill align-items-center container-fluid'>

    <div class = 'd-flex flex-fill float-start'>
        <i class=''></i>
        <a class='text-decoration-none' href='https://www.yiiframework.com/' target='_blank' rel='noopener'>
            Yii Framework - <?= date('Y') ?>
        </a>
    </div>

    <div class='float-end'>
        <a class='text-decoration-none px-1' href='https://github.com/yiisoft' target='_blank' rel='noopener' >
            <svg class="bi text-white" width="24" height="24">
                <use xlink:href="<?= $aliases->get('@icons') . '/' . 'bootstrap-icons.svg#github' ?>"/>
            </svg>
        </a>
        <a class='text-decoration-none px-1' href='https://join.slack.com/t/yii/shared_invite/enQtMzQ4MDExMDcyNTk2LTc0NDQ2ZTZhNjkzZDgwYjE4YjZlNGQxZjFmZDBjZTU3NjViMDE4ZTMxNDRkZjVlNmM1ZTA1ODVmZGUwY2U3NDA' target='_blank' rel='noopener'>
            <svg class="bi text-white" width="24" height="24">
                <use xlink:href="<?= $aliases->get('@icons') . '/' . 'bootstrap-icons.svg#slack' ?>"/>
            </svg>
        </a>
        <a class='text-decoration-none px-1' href='https://www.facebook.com/groups/yiitalk' target='_blank' rel='noopener'>
            <svg class="bi text-white" width="24" height="24">
                <use xlink:href="<?= $aliases->get('@icons') . '/' . 'bootstrap-icons.svg#facebook' ?>"/>
            </svg>
        </a>
        <a class='text-decoration-none px-1' href='https://twitter.com/yiiframework' target='_blank' rel='noopener'>
            <svg class="bi text-white" width="24" height="24">
                <use xlink:href="<?= $aliases->get('@icons') . '/' . 'bootstrap-icons.svg#twitter' ?>"/>
            </svg>
        </a>
        <a class='text-decoration-none px-1' ref='https://t.me/yii3ru' target='_blank' rel='noopener'>
            <svg class="bi text-white" width="24" height="24">
                <use xlink:href="<?= $aliases->get('@icons') . '/' . 'bootstrap-icons.svg#chat-text' ?>"/>
            </svg>
        </a>
    </div>
</div>
