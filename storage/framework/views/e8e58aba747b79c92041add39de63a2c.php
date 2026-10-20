

<?php $__env->startSection('Contents'); ?>
<?php
    $approvalStatus = $data['approval_status'] ?? null;
    $currentStateClass = $approvalStatus === '1' || $approvalStatus === 1
        ? 'approval-status-approved'
        : ($approvalStatus === '2' || $approvalStatus === 2
            ? 'approval-status-rejected'
            : 'approval-status-pending');
    $currentState = $currentStateClass === 'approval-status-approved'
        ? 'Approved'
        : ($currentStateClass === 'approval-status-rejected' ? 'Rejected' : 'Pending');
?>

<style>
    .approval-page {
        max-width: 1040px;
        margin: 0 auto;
    }

    .approval-hero {
        position: relative;
        overflow: hidden;
        padding: 30px;
        color: #fff;
        border-radius: 12px;
        background: linear-gradient(120deg, #123b5d 0%, #087e8b 100%);
        box-shadow: 0 8px 24px rgba(18, 59, 93, .18);
    }

    .approval-hero:after {
        position: absolute;
        top: -75px;
        right: -45px;
        width: 240px;
        height: 240px;
        content: "";
        border: 35px solid rgba(255, 255, 255, .08);
        border-radius: 50%;
    }

    .approval-hero-content {
        position: relative;
        z-index: 1;
    }

    .approval-eyebrow {
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.6px;
        text-transform: uppercase;
        opacity: .78;
    }

    .approval-hero h2 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
    }

    .approval-reference {
        opacity: .82;
    }

    .approval-status-panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 18px;
        padding: 16px 18px;
        border-radius: 9px;
        background: #f4f8fb;
    }

    .approval-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .approval-detail {
        min-width: 0;
        padding: 14px 16px;
        border: 1px solid #e8edf2;
        border-radius: 9px;
        background: #fff;
    }

    .approval-detail-label {
        display: block;
        margin-bottom: 5px;
        color: #718096;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .approval-detail-value {
        color: #243b53;
        font-size: 14px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .approval-status-panel strong {
        display: block;
        margin-bottom: 4px;
        color: #243b53;
        font-size: 15px;
    }

    .approval-status-panel > div > span {
        color: #718096;
        font-size: 13px;
    }

    .approval-state-pill {
        padding: 7px 12px;
        white-space: nowrap;
        font-size: 12px;
        font-weight: 700;
        border-radius: 20px;
    }

    .approval-status-approved {
        color: #176b45;
        background: #dff4e8;
    }

    .approval-status-pending {
        color: #8a5a00;
        background: #fff1cc;
    }

    .approval-status-rejected {
        color: #a12c36;
        background: #fde3e5;
    }

    @media (max-width: 767px) {
        .approval-hero {
            padding: 20px;
        }

        .approval-hero h2 {
            font-size: 23px;
        }

        .approval-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Digital Approval<small>hrms</small></h1>
    </section>

    <section class="content">
        <div class="approval-page col-lg-6 col-md-8 col-xs-12">
            <div class="approval-hero">
                <div class="approval-hero-content">
                    <div class="approval-eyebrow">Human Resources Management System</div>
                    <h2>Approval Verification</h2>
                    <div class="approval-reference">
                        <?php echo e($tb_name); ?> &nbsp;|&nbsp; Doc.ID #<?php echo e($id); ?> &nbsp;|&nbsp; Created at <?php echo e(!empty($data['created_at']) ? \Carbon\Carbon::parse($data['created_at'])->format('d M Y H:i') : '-'); ?>

                    </div>
                </div>
                <div class="approval-status-panel">
                    <div>
                        <strong>Approval Status <?php echo e($data['pos_mame'] ?? '-'); ?></strong>

                    </div>
                    <span class="approval-state-pill <?php echo e($currentStateClass); ?>"><?php echo e($currentState); ?></span>
                </div>
                <div class="approval-details">
                    <div class="approval-detail">
                        <span class="approval-detail-label">Employee Name</span>
                        <span class="approval-detail-value"><?php echo e($data['employee_name'] ?? '-'); ?></span>
                    </div>
                    <div class="approval-detail">
                        <span class="approval-detail-label">NIK</span>
                        <span class="approval-detail-value"><?php echo e($data['NIK'] ?? '-'); ?></span>
                    </div>
                    <div class="approval-detail">
                        <span class="approval-detail-label">Position</span>
                        <span class="approval-detail-value"><?php echo e($data['position_name'] ?? '-'); ?></span>
                    </div>
                    <div class="approval-detail">
                        <span class="approval-detail-label">Approval Date Time</span>
                        <span class="approval-detail-value">
                            <?php echo e(!empty($data['approve_date']) ? \Carbon\Carbon::parse($data['approve_date'])->format('d M Y H:i') : '-'); ?>

                        </span>
                    </div>
                </div>
                <br>
                <div class="approval-hero-content">
                    <div class="approval-reference">
                        More Detail Please Click <u><a href="<?php echo e(url($data['link'])); ?>" target="_blank" style="color:#fff;">Approval Link</a></u>
                    </div>
                </div>

        </div>

        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/approval/index.blade.php ENDPATH**/ ?>