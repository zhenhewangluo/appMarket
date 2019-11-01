
package com.market.hjapp.network;

import java.io.IOException;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.content.Context;

public final class NetworkManager {
	
	public static HashMap<String, Object> getDuoBaoUrl(Context ctx) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getDuoBaoUrl(ctx);
	}
	public static HashMap<String, Object> getSelfVerUpgrade(Context ctx) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getSelfVerUpgrade(ctx);
	}
    
	public static HashMap<String, Object> getAppInfoList(Context ctx, String applist) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getAppInfoList(ctx, applist);
	}
	
	public static HashMap<String, Object> getComments(Context ctx, String appid) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getComments(ctx, appid);
	}
	
	public static HashMap<String, Object> comment(Context ctx, String appid, String rating, String content)
			throws ClientProtocolException, IOException, JSONException {
		return HTTParser.comment(ctx, appid, rating, content);
	}
	public static HashMap<String, Object> score(Context ctx, String appid, String rating)
			throws ClientProtocolException, IOException, JSONException {
		return HTTParser.score(ctx, appid, rating);
}

    public static HashMap<String, Object> search(Context ctx, String word) throws ClientProtocolException, IOException, JSONException {
        return HTTParser.search(ctx, word);
    }

    public static HashMap<String, Object> pay(Context ctx, String parentId, String appid, String price, String passwd) 
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.pay(ctx, parentId, appid, price, passwd);
    }

    public static HashMap<String, Object> charge(Context ctx, String aid, String amount, String limit, 
            String serial, String passwd) throws ClientProtocolException, IOException, JSONException {
        return HTTParser.charge(ctx, aid, amount, limit, serial, passwd);
    }
    
    public static HashMap<String, Object> anonymousLogin(Context ctx) 
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.anonymousLogin(ctx);
    }

    public static HashMap<String, Object> getCategoryList(Context ctx) 
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.getCategoryList(ctx);
    }

    public static HashMap<String, Object> downloadApp(Context ctx, String appid, String payid, String source) 
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.downloadApp(ctx, appid, payid, source);
    }

    public static HashMap<String, Object> register(Context ctx, String username, String password, String nickname)
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.register(ctx, username, password, nickname);
    }

    public static HashMap<String, Object> login(Context ctx, String username, String password)
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.login(ctx, username, password,"");
    }
    public static HashMap<String, Object> loginAndSetName(Context ctx, String username, String password, String name)
    throws ClientProtocolException, IOException, JSONException {
return HTTParser.login(ctx, username, password,  name);
}
    public static HashMap<String, Object> setUserInfo(Context ctx, String phone, String name,String type)
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.setUserInfo(ctx, phone, name,type );
    }
    
    public static HashMap<String, Object> setPhone(Context ctx, String phone, String validate)
    throws ClientProtocolException, IOException, JSONException {
return HTTParser.setPhone(ctx, phone, validate );
}
    
    public static HashMap<String, Object> SendVerify(Context ctx, String phone)
    throws ClientProtocolException, IOException, JSONException {
return HTTParser.SendVerify(ctx, phone);
}
    public static HashMap<String, Object> getChargeList(Context ctx)
	    throws ClientProtocolException, IOException, JSONException {
    	return HTTParser.getChargeList(ctx);
	}

    public static HashMap<String, Object> getScreenshotList(Context ctx, String appid)
            throws ClientProtocolException, IOException, JSONException {
        return HTTParser.getScreenshotList(ctx, appid);
    }

    public static HashMap<String, Object> logout(Context ctx) throws ClientProtocolException, 
            IOException, JSONException {
        return HTTParser.logout(ctx);
    }
    
    public static HashMap<String, Object> findPassword(Context ctx, String userEmail)
    		throws ClientProtocolException, IOException, JSONException {
    	return HTTParser.findPassword(ctx, userEmail);
    }
    
    public static HashMap<String, Object> changePassword(Context ctx, String oldPassword, String newPassword)
			throws ClientProtocolException, IOException, JSONException {
    	return HTTParser.changePassword(ctx, oldPassword, newPassword);
	}
    
    public static HashMap<String, Object> getMyRating(Context ctx, String appid)
			throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getMyRating(ctx, appid);
	}
    
    public static HashMap<String, Object> imageLoader(Context ctx, String imageUrl)
			throws ClientProtocolException, IOException, JSONException {
    	return HTTParser.imageLoader(ctx, imageUrl);
    }		
    
    public static HashMap<String, Object> getAppStateList(Context ctx, String applist) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getAppStateList(ctx, applist);
	}

    
    public static HashMap<String, Object> authenticate(Context ctx, String appid) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.authenticate(ctx, appid);
	}
    
	public static HashMap<String, Object> postDownloadSuccess(Context ctx, String pid, String downloadId) 
	        throws ClientProtocolException, IOException, JSONException {
		return HTTParser.postDownloadSuccess(ctx, pid, downloadId);
	}
	
	public static HashMap<String, Object> getLocalApplist(Context ctx, String applist) 
		    throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getLocalApplist(ctx, applist);
	}
	
	public static HashMap<String, Object> uploadUserLog(Context ctx, String userLogList) 
	    	throws ClientProtocolException, IOException, JSONException {
		return HTTParser.uploadUserLogList(ctx, userLogList);
	}
	
	public static HashMap<String, Object> getNewestCateData(Context ctx, String appid) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getNewestCateData(ctx, appid);
	}
	
	public static HashMap<String, Object> getOneCateData(Context ctx, String cateid) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getOneCateApplist(ctx, cateid);
	}
	
	public static HashMap<String, Object> sendSuggestion(Context ctx, String suggestion, String email) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.sendSuggestion(ctx, suggestion, email);
	}
	
	public static HashMap<String, Object> getRelatedAppList(Context ctx, String appid) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getRelatedAppList(ctx, appid);
	}
	
	public static HashMap<String, Object> getFavoriteChannel(Context ctx) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getFavoriteChannel(ctx);
	}
	
	public static HashMap<String, Object> setFavoriteChannel(Context ctx, String channelList) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.setFavoriteChannel(ctx, channelList);
	}
	
	public static HashMap<String, Object> getRecommmend(Context ctx, String recommendId) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getRecommmend(ctx, recommendId);
	}
	public static HashMap<String, Object> getRecommmendByTime(Context ctx,String time) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getRecommmendByTime(ctx,time);
	}
	public static HashMap<String, Object> clientCheck(Context ctx) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.clientCheck(ctx);
	}
	
	public static HashMap<String, Object> getOnecateApplistNew(Context ctx, String cateid, String type) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getOnecateApplistNew(ctx,cateid,type);
	}
	public static HashMap<String, Object> getCateListNew(Context ctx,String updatetime) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getCateListNew(ctx,updatetime);
	}
	public static HashMap<String, Object> getHotwordsList(Context ctx,String count) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getHotwordsList(ctx,count);
	}
	public static HashMap<String, Object> getOneCateApplistPage(Context ctx, String cateid,String type,
			String orderType,String pageNo,String perpage) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getOneCateApplistPage(ctx, cateid,type,orderType,pageNo,perpage);
	}
	
	public static HashMap<String, Object> getInfoVersion(Context ctx, String appid,String infoVersion) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getInfoVersion(ctx, appid ,infoVersion);
	}
	public static HashMap<String, Object> getEventTimes(Context ctx, String updatetime) throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getEventTimes(ctx, updatetime);
	}
	public static HashMap<String, Object> getBackupAndRecoveryList(Context ctx,String type,String applist)throws ClientProtocolException, IOException, JSONException {
		return HTTParser.getBackupAndRecoveryList(ctx, type,applist);
	}
	
	public static HashMap<String, Object> downloadAppList(Context ctx, String appidList, String source) 
	    throws ClientProtocolException, IOException, JSONException {
		return HTTParser.downloadAppList(ctx, appidList, source);
	}
}
