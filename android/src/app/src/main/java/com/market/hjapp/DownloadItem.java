
package com.market.hjapp;

public class DownloadItem {
	
    
    private int mDbPid;
	private int mAppid;
	private String mUrl;
	private String mDownloadid;
	private String mAppName;
	private int mDownloadCount;

			
	public DownloadItem(int dbid, int appid, String url, String downloadid, String appName, int downloadCount)
	{
		mDbPid = dbid;
		mAppid = appid;
		mUrl = url;
		mDownloadid = downloadid;
		mAppName = appName;
		mDownloadCount = downloadCount;
	}
	
	public int getDBId()
	{
		return mDbPid;
	}
	
	public int getAppId()
	{
		return mAppid;
	}
	
	public String getUrl()
	{
		return mUrl;
	}
	
	public String getDownloadId()
	{
		return mDownloadid;
	}
	
	public String getAppName()
	{
		return mAppName;
	}
	
	public int getDownloadCount()
	{
		return mDownloadCount;
	}
}
