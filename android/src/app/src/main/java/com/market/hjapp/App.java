package com.market.hjapp;

import java.util.ArrayList;

public class App {

	// for status saved in provider
	public static final int INIT = 0;// 初始化
	public static final int DOWNLOADING = 1;// 下载中
	public static final int PAUSED = 2;// 暂停
	public static final int DOWNLOADED = 3;// 下载完成
	public static final int INSTALLED = 4;// 安装
	public static final int HAS_UPDATE = 5;// 已更新

	public static final int INVALID_ID = -1;// 无效ID

	private int mDbPid = INVALID_ID;
	private int mId;
	private String mName;
	private Author mAuthor;
	private String mFileName;
	private String mLogoUrl;
	private String mIconUrl;
	private float mRate;
	private String mPrice;
	private int mVersion;
	private String mAppVersion;
	private int mDownloadCount;
	private long mSize;
	private String mLongDesc;
	private int mHit;
	// private String mSlogan;
	private String mPath;
	private String mPackageName;
	private int mDownloadedStatus;
	private int mDownloadedSize;
	private int mStatus = INIT;
	// private boolean mIsDownloaded;
	// private boolean mIsInstalled;
	private boolean mIsPaid;
	private boolean mIsError;

	private ArrayList<Comment> mComment;
	// private ArrayList<ScreenShot> mScreenshots;
	private String mDownloadId;

	// by default it's 0
	private String mPayId = "0";
	private String mDesc;
	private String mAuthorName;
	// private int mRatingUpCount;
	// private int mRatingDownCount;
	private int mInfoVersion;
	private String mScreenshotUrl;
	private String mDownloadPath;
	private String mLocalPath;

	private String mDownloadTime;
	private String mNickName;

	private int mScore;
	private int mScoreCount;
	private int mCommentCount;
	private int mLanguage;

	private int mLastUpdateTime;

	@Override
	public String toString() {
		final StringBuilder sb = new StringBuilder();
		sb.append("mDbPid : ").append(mDbPid).append("\nmPid : ").append(mId)
				.append("\nmName : ").append(mName).append("\nmFileName : ")
				.append(mFileName).append("\nmLogoUrl : ").append(mLogoUrl)
				.append("\nmRate : ").append(mRate).append("\nmPrice : ")
				.append(mPrice).append("\nmVersion").append(mVersion)
				.append("\nmAppVersion : ").append(mAppVersion)
				.append("\nmDownloadTimes : ").append(mDownloadCount)
				.append("\nmSize : ").append(mSize)
				.append("\nmLongDesc : ")
				.append(mLongDesc)
				.append("\nmHit : ")
				.append(mHit)
				// .append("\nmSlogan : ").append(mSlogan)
				.append("\nmPath : ").append(mPath).append("\nmPackageName : ")
				.append(mPackageName).append("\nmDownloadedStatus : ")
				.append(mDownloadedStatus)
				.append("\nmDownloadedSize : ")
				.append(mDownloadedSize)
				// .append("\nmIsDownloaded : ").append(mIsDownloaded)
				// .append("\nmIsInstalled : ").append(mIsInstalled)
				.append("\nmIsPaid : ").append(mIsPaid)
				.append("\n\nmAuthor : ").append(mAuthor);
		for (Comment comm : mComment) {
			sb.append("\n\n").append(comm);
		}

		return sb.toString();
	}

	public void setDbPid(int id) {
		mDbPid = id;
	}

	public void setId(int id) {
		mId = id;
	}

	public void setName(String name) {
		mName = name;
	}

	public void setAuthor(Author author) {
		mAuthor = author;
	}

	public void setFileName(String fileName) {
		mFileName = fileName;
	}

	public void setLogoUrl(String logoUrl) {
		mLogoUrl = logoUrl;
	}

	public void setRate(float rate) {
		mRate = rate;
	}

	public void setPrice(String price) {
		mPrice = price;
	}

	public void setVersion(int version) {
		mVersion = version;
	}

	public void setAppVersion(String appVersion) {
		mAppVersion = appVersion;
	}

	public void setDownloadCount(int count) {
		mDownloadCount = count;
	}

	public void setSize(long size) {
		mSize = size;
	}

	public void setLongDesc(String longDesc) {
		mLongDesc = longDesc;
	}

	public void setHit(int hit) {
		mHit = hit;
	}

	// public void setSlogan(String slogan) {
	// mSlogan = slogan;
	// }
	//
	public void setDownloadedStatus(int downloadedStatus) {
		mDownloadedStatus = downloadedStatus;
	}

	public void setDownloadedSize(int downloadedSize) {
		mDownloadedSize = downloadedSize;
	}

	public void setPath(String path) {
		mPath = path;
	}

	public void setComment(ArrayList<Comment> comment) {
		mComment = comment;
	}

	public void setStatus(int status) {
		mStatus = status;
	}

	// public void setIsDownloaded(boolean isDownloaded) {
	// mIsDownloaded = isDownloaded;
	// }

	// public void setIsInstalled(boolean isInstalled) {
	// mIsInstalled = isInstalled;
	// }

	public void setIsPaid(boolean isPaid) {
		mIsPaid = isPaid;
	}

	public void setIsError(boolean isError) {
		mIsError = isError;
	}

	public int getDbId() {
		return mDbPid;
	}

	public int getId() {
		return mId;
	}

	public String getName() {
		return mName;
	}

	public Author getAuthor() {
		return mAuthor;
	}

	public String getFileName() {
		return mFileName;
	}

	public String getLogoUrl() {
		return mLogoUrl;
	}

	public String getPrice() {
		return mPrice;
	}

	public int getVersion() {
		return mVersion;
	}

	public String getAppVersion() {
		return mAppVersion;
	}

	public int getDownloadCount() {
		return mDownloadCount;
	}

	public long getSize() {
		return mSize;
	}

	public String getLongDesc() {
		return mLongDesc;
	}

	public int getHit() {
		return mHit;
	}

	// public String getSlogan() {
	// return mSlogan;
	// }

	public String getPath() {
		return mPath;
	}

	public String getPackgeName() {
		return mPackageName;
	}

	public int getDownloadedStatus() {
		return mDownloadedStatus;
	}

	public int getDownloadedSize() {
		return mDownloadedSize;
	}

	public ArrayList<Comment> getComment() {
		return mComment;
	}

	public int getStatus() {
		return mStatus;
	}

	// public boolean getIsDownloaded() {
	// return mIsDownloaded;
	// }

	// public boolean getIsInstalled() {
	// return mIsInstalled;
	// }

	public boolean getIsPaid() {
		return mIsPaid;
	}

	public boolean getIsError() {
		return mIsError;
	}

	// public void setScreenshots(ArrayList<ScreenShot> screenshotList) {
	// mScreenshots = screenshotList;
	// }
	//
	// public ArrayList<ScreenShot> getScreenshots() {
	// return mScreenshots;
	// }

	public void setDownloadId(String downloadId) {
		mDownloadId = downloadId;
	}

	public String getDownloadId() {
		return mDownloadId;
	}

	public void setPayId(String id) {
		mPayId = id;
	}

	public String getPayId() {
		return mPayId;
	}

	public void setIconUrl(String url) {
		mIconUrl = url;
	}

	public String getIconUrl() {
		return mIconUrl;
	}

	@Override
	public boolean equals(Object o) {
		if (!(o instanceof App)) {
			return false;
		}

		return mId == ((App) o).mId;
	}

	public void setDescription(String desc) {
		mDesc = desc;
	}

	public String getDescription() {
		return mDesc;
	}

	public void setAuthorName(String name) {
		mAuthorName = name;
	}

	public String getAuthorName() {
		return mAuthorName;
	}

	public void setInfoVersion(int v) {
		mInfoVersion = v;
	}

	public int getInfoVersion() {
		return mInfoVersion;
	}

	public void setScreenshotUrl(String url) {
		mScreenshotUrl = url;
	}

	public String getScreenshotUrl() {
		return mScreenshotUrl;
	}

	public String getDownloadPath() {
		return mDownloadPath;
	}

	public void setDownloadPath(String path) {
		mDownloadPath = path;
	}

	public void setPackageName(String name) {
		mPackageName = name;
	}

	public void setLocalPath(String path) {
		mLocalPath = path;
	}

	public String getLocalPath() {
		return mLocalPath;
	}

	public void setDownloadTime(String downloadTime) {
		mDownloadTime = downloadTime;
	}

	public String getNickName() {
		return mNickName;
	}

	public void setNickName(String nickname) {
		mNickName = nickname;
	}

	public String getDownloadTime() {
		return mDownloadTime;
	}

	public void setScore(int score) {
		mScore = score;
	}

	public int getScore() {
		return mScore;
	}

	public void setScoreCount(int scoreCount) {
		mScoreCount = scoreCount;
	}

	public int getScoreCount() {
		return mScoreCount;
	}

	public void setCommentCount(int commentCount) {
		mCommentCount = commentCount;
	}

	public int getCommentCount() {
		return mCommentCount;
	}

	public void setLanguage(int language) {
		mLanguage = language;
	}

	public int getLanguage() {
		return mLanguage;
	}

	public int getLastUpdateTime() {
		return mLastUpdateTime;
	}

	public void setLastUpdateTime(int lastUpdateTime) {
		mLastUpdateTime = lastUpdateTime;
	}

	/**
	 * com.market.hjapp App 定义状态及设置、获取方法 AppStoreApplication 标注下载错误信息 AppTabSpec
	 * 从服务器获取信息存入本地SQL Author 定义各人信息及获取方法 Base64 获取ALPHABET Category 分类信息
	 * ChargeChannel 充值界面数据信息 Comment 评价界面数据信息 ConstantValues 服务端口名称
	 * DowmloadItem 定义应用加载信息 GeneralUtil 根据用户手机型号、屏幕、品牌设置应用参数 ImageLoader 图片加载类
	 * MyLog 输出方法 Recommend 推荐界面数据信息 ScreenShot 截屏数据信息 SecurityUtil
	 * 用户密码、个人资料的转码、加密 SubCate 数值传递 User 用户的使用状态 UserInfo 用户的个人信息
	 * com.market.hjapp.database DatabaseHelper 数据库文件名 DatabaseSchema 定义数据库变量
	 * DatabaseUtils 数据库逻辑部分 com.market.hjapp.network HTTParser HTTPUtil
	 * NetworkManager com.market.hjapp.receiver SystemBroadcastReceiver
	 * com.market.hjapp.service AppService Decrypt UpdateDataService
	 * UploadLocalAppService IAppServiceInterface
	 * com.market.hjapp.service.download DownloadManager FileManager
	 * FileManipulation OnDowmloadStatusChangedListener
	 * com.market.hjapp.service.download.entity DownloadErrorInfo DownloadInfo
	 * DownloadInfoType DownloadProgressInfo com.market.hjapp.ui.activity
	 * AboutAcitiviy aboutSilverActivity AppDetailActivity AuthenticateAcitvity
	 * BackupAppListActivity BaseActivity BaseBottomTabAcitivity
	 * BrowseCategoryListActivity BrowseCommentListActivity
	 * BrowseManageListAcitvity BrowseRankListAcitivity
	 * BrowseSuggestedAppListActivity CategoryAppListAcitivity
	 * ChangePasswordDialogAcitivity ChargeDialogActivity
	 * ChargeListDialogActivity ChooseShareOptionDialogAcitivity
	 * CommentAcitivity EditUserInfoDialogActivity FindPasswordDialogActivity
	 * GradeAcitvity InviteInfoAcitvity Lead2Activity LeadActivity
	 * LeadLoginActivity LoginAccountActivity LoginDialogAcitivity
	 * MidwareAcitvity MyAccountActivity MyAccountDialogActivity
	 * MyDownloadsActivity PayActivity RankAppListActivity RecommendActivity
	 * RecoveryAppListActivity RegisterDialogActivity RSATestActivity
	 * SerchedAppListActivity SearchedResultAcitvity SelectFavoriteCateActivity
	 * SettingActivity SplashActivity SuggestionActivity UpdateActivity
	 * ViewScreenshotsActivity com.market.hjapp.ui.adapter AppListAdapter
	 * BackupAppListAdapter CategoryAppListAdapter CategoryListAdapter
	 * CommentListAdapter DownloadListAdapter ImageAdapter
	 * MyDowonloadListAdapter RankListAdapter RecoveryAppListAdapter
	 * SearchResultListAdapter com.market.hjapp.ui.tasks AnonymousLoginTask
	 * AuthenticateTask BaseAsyncTask ChangePassowrdTask ChargeTask
	 * CommentGradeTask CommentTask DownloadAppListTask EditUserinfoTask
	 * FindPasswordTask GetAppInfoListTask GetAppInfoVersionTask
	 * GetAppStateListTask GetBackupAndRecoveryListTask GetCategoryListTask
	 * GetChargeListTask GetCommentListTask GetFavoriteCateListTask
	 * GetHotwordsListTask GetLocalAppListTask GetMyRatingTask
	 * GetNewestCateDataTask GetOneCateDataTask GetRecommendTask
	 * GetRelateAppListTask ImageLoaderTask LaunchAppTask LoadScreenshotTask
	 * LoginTask LogoutTask PayTask ProcessInstallTask RegisterTask SearchTask
	 * SendSuggestionTask SetFavoriteCateListTask UpdateDownloadDynamicTask
	 * com.market.hjapp.ui.view AppItemView FancyProgressBar LongButton
	 * MyDialogPreFerence RatingStars ScreenshotIndicatorsView
	 */

}
