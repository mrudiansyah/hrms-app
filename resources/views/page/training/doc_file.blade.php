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
            @php
                $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $encodedName = rawurlencode($file_name);
                $fileUrl = url('/Show/' . $encodedName);
            @endphp

            @if($file_name == '')
                <div class="text-center text-muted" style="padding: 40px;">
                    <i class="fa fa-file-o" style="font-size: 48px;"></i>
                    <p style="margin-top: 10px;">Pilih dokumen untuk preview</p>
                </div>
            @elseif(in_array($extension, ['mp4', 'webm', 'ogg']))
                <div class="embed-responsive embed-responsive-16by9">
                    <video class="embed-responsive-item" controls>
                        <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>
                </div>
            @elseif($extension == 'pdf')
                <div id="gamebox" style="width:100%; height:1000px;">
                    <iframe src="{{ $fileUrl }}" width="100%" height="100%" style="border:none;" id="documents"
                        allowfullscreen>
                    </iframe>
                </div>
            @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                <div class="text-center" style="padding: 20px;">
                    <img src="{{ $fileUrl }}" alt="{{ $file_name }}"
                        style="max-width:100%; max-height:800px; object-fit:contain;">
                </div>
            @elseif(in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
                <div id="gamebox" style="width:100%; height:1000px;">
                    <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" width="100%"
                        height="100%" style="border:none;" id="documents" allowfullscreen>
                    </iframe>
                </div>
            @else
                <div class="text-center text-muted" style="padding: 40px;">
                    <i class="fa fa-exclamation-triangle" style="font-size: 48px;"></i>
                    <p style="margin-top: 10px;">Format file <strong>.{{ $extension }}</strong> tidak dapat di-preview.</p>
                    <a href="{{ $fileUrl }}" class="btn btn-primary btn-sm" download>
                        <i class="fa fa-download"></i> Download File
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>