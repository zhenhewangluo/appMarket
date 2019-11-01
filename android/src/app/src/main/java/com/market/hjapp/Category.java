
package com.market.hjapp;

public class Category {
    
    private String mName;
    private String mIconUrl;
    private int mCateId;
    private int mParentId;
    private int mAppCount;
    private int mCateOrder;
    
    private String mAppList1;
    private String mAppList2;
    private String mAppList3;
    
    public static final int CATE_TYPE_MAIN = 1;//推荐应用
    public static final int CATE_TYPE_CLASS = 2;
    public static final int CATE_TYPE_TOPIC = 3;//分类应用
    public static final int CATE_TYPE_RANK = 4;//排行应用
    
    private int mType = CATE_TYPE_CLASS; 
    
    private long mUpdateInterval;
    private long mLastUpdateTime;
    
    private String mDescription;

    public int getSig() {
        return mCateId;
    }

    public void setSig(int sig) {
        mCateId = sig;
    }
    
    public int getParentId() {
        return mParentId;
    }

    public void setParentId(int parentId) {
    	mParentId = parentId;
    }
    
    public int getType() {
        return mType;
    }

    public void setType(int sig) {
    	mType = sig;
    }
    
    public String getName() {
        return mName;
    }

    public void setName(String name) {
        mName = name;
    }

    public String getIconUrl() {
        return mIconUrl;
    }

    public void setIconUrl(String iconUrl) {
        mIconUrl = iconUrl;
    }

    public int getAppCount() {
        return mAppCount;
    }
    
    public void setAppCount(int c) {
        mAppCount = c;
    }
    
    public String getAppList1() {
        return mAppList1;
    }
    
    public void setAppList1(String list) {
    	mAppList1 = list;
    }
    
    public String getAppList2() {
        return mAppList2;
    }
    
    public void setAppList2(String list) {
    	mAppList2 = list;
    }
    
    public String getAppList3() {
        return mAppList3;
    }
    
    public void setAppList3(String list) {
    	mAppList3 = list;
    }
    
    public long getUpdateInterval() {
        return mUpdateInterval;
    }
    
    public void setUpdateInterval(long updateInterval) {
    	mUpdateInterval = updateInterval;
    }
    
//    public long getLastUpdateTime() {
//        return mLastUpdateTime;
//    }
//    
//    public void setLastUpdateTime(long lastUpdateInterval) {
//    	mLastUpdateTime = lastUpdateInterval;
//    }
    
    
    public String getDescription() {
        return mDescription;
    }
    
    public void setDescription(String description) {
    	mDescription = description;
    }

	public int getCateOrder() {
		return mCateOrder;
	}

	public void setCateOrder(int mCateOrder) {
		this.mCateOrder = mCateOrder;
	}
    
}
