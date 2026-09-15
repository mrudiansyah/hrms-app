<div class="d-flex justify-content-center">
    <div class="box box-primary" style="background:#FFF;">
        <div class="box-header">
            <i class="fa fa-tv"></i>
            <h3 class="box-title">Preview Document</h3>
            <div class="pull-right">
                &nbsp;
            </div>
        </div>
        <div class="box-body">
            <?php
                $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $encodedName = rawurlencode($file_name);
                $fileUrl = asset('storage/' . $encodedName);
            ?>

            <?php if($file_name == ''): ?>
                <div class="text-center text-muted" style="padding: 40px;">
                    <i class="fa fa-file-o" style="font-size: 48px;"></i>
                    <p style="margin-top: 10px;">Pilih dokumen untuk preview</p>
                </div>
            <?php elseif(in_array($extension, ['mp4', 'webm', 'ogg'])): ?>
                <div class="embed-responsive embed-responsive-16by9">
                    <video class="embed-responsive-item" controls>
                        <source src="<?php echo e($fileUrl); ?>" type="video/<?php echo e($extension); ?>">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>
                </div>
            <?php elseif($extension == 'pdf'): ?>
                <div id="gamebox" style="width:100%; height:1000px;">
                    <iframe src="<?php echo e($fileUrl); ?>" width="100%" height="100%" style="border:none;" id="documents"
                        allowfullscreen>
                    </iframe>
                </div>
            <?php elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                <div class="text-center" style="padding: 20px;">
                    <img src="<?php echo e($fileUrl); ?>" alt="<?php echo e($file_name); ?>"
                        style="max-width:100%; max-height:800px; object-fit:contain;">
                </div>
            <?php elseif(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
                <div id="gamebox" style="width:100%; height:1000px;">
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?php echo e(urlencode($fileUrl)); ?>" width="100%"
                        height="100%" style="border:none;" id="documents" allowfullscreen>
                    </iframe>
                </div>
            <?php else: ?>
                <div class="text-center text-muted" style="padding: 40px;">
                    <i class="fa fa-exclamation-triangle" style="font-size: 48px;"></i>
                    <p style="margin-top: 10px;">Format file <strong>.<?php echo e($extension); ?></strong> tidak dapat di-preview.</p>
                    <a href="<?php echo e($fileUrl); ?>" class="btn btn-primary btn-sm" download>
                        <i class="fa fa-download"></i> Download File
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\training\doc_file.blade.php ENDPATH**/ ?>