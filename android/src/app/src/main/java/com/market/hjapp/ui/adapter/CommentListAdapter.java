
package com.market.hjapp.ui.adapter;

import java.util.ArrayList;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.market.hjapp.Comment;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;

public class CommentListAdapter extends BaseAdapter {
    private static final String TAG = "CommentListAdapter";
    
    private LayoutInflater mInflater;
    private ArrayList<Comment> mData;
    private Context mContext;
    
    public CommentListAdapter(Context context) {
        mInflater = LayoutInflater.from(context);
        mContext = context;
        mData = new ArrayList<Comment>();
    }

    @Override
    public int getCount() {
        return mData.size() + 1;
    }

    @Override
    public Object getItem(int pos) {
    	if (pos > 0)
    		return mData.get(pos-1);
    	else
    		return null;
    }
    
    @Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return position;
	}

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
    	MyLog.d(TAG, "getView>>>>>>>>>>>>>>>>>>>>>>>>>>>");
    	if (position == 0 )
    	{
    		TextView tv;
    		if (convertView == null || !(convertView instanceof TextView))
    		{
    			tv = (TextView)mInflater.inflate(R.layout.list_top_textview, null);
    		}
    		else
    			tv =  (TextView)convertView;

    		tv.setText(R.string.comment_top);

    		return tv;
    	}
    	
    	ViewHolder holder;
		if (convertView == null || (convertView instanceof TextView)) {
			holder = new ViewHolder();
			convertView = mInflater.inflate(R.layout.comment_item, null);
			holder.user=(TextView)convertView.findViewById(R.id.comment_username);
			holder.time=(TextView)convertView.findViewById(R.id.comment_time);
			holder.content=(TextView)convertView.findViewById(R.id.comment_content);
			convertView.setTag(holder);
		}
		else {
			holder = (ViewHolder) convertView.getTag();
		}
		
		Comment comment = mData.get(position-1);
		MyLog.d(TAG, "start showing app " + position);

		// user
		MyLog.d(TAG, "user: " + comment.getName());
		holder.user.setText(comment.getName());

		// time
		holder.time.setText(comment.getTime());
		MyLog.d(TAG, "time: " + comment.getTime());

		// comment content
		holder.content.setText(comment.getContent());
		MyLog.d(TAG, "name: " + comment.getContent());
		return convertView;

    	
//    	if (position == 0 )
//        {
//        	TextView tv;
//        	if (convertView == null || !(convertView instanceof TextView))
//        	{
//        		tv = (TextView)mInflater.inflate(R.layout.list_top_textview, null);
//        	}
//        	else
//        		tv =  (TextView)convertView;
//        	
//        	tv.setText(R.string.comment_top);
//        	
//        	return tv;
//        }
//    	
//        View itemView;
//        if (convertView == null || (convertView instanceof TextView))
//        {
//            itemView = mInflater.inflate(R.layout.comment_item, null);
//        } else {
//            itemView = convertView;
//        }
//
//        Comment comment = mData.get(position-1);
//        MyLog.d(TAG, "start showing app " + position);
//                
//        // user
//        MyLog.d(TAG, "user: " + comment.getName());
//        TextView user = (TextView)itemView.findViewById(R.id.comment_username);
//        user.setText(comment.getName());
//        
//        // time
//        TextView time = (TextView)itemView.findViewById(R.id.comment_time);
//        time.setText(comment.getTime());
//        MyLog.d(TAG, "time: " + comment.getTime());
//        
//        // rating
////        RatingStars rating = (RatingStars)itemView.findViewById(R.id.comment_rating);
//////        rating.setStarParam(11, 9, 1, R.drawable.z54, R.drawable.z53);
////        rating.setStarParam(R.drawable.z54, R.drawable.z53);
////        
////        MyLog.d(TAG, "comment.getRate():" + comment.getRate());
////        rating.setRating(Integer.parseInt(comment.getRate()));
//        
////        TextView rating = (TextView)itemView.findViewById(R.id.comment_rating);
////        if (comment.getRate().equals("up"))
////        {
////        	rating.setText(R.string.comment_rating_up);
////        	rating.setTextColor(0xffff76cd);
////        }
////        else if (comment.getRate().equals("down"))
////        {
////        	rating.setText(R.string.comment_rating_down);
////        	rating.setTextColor(0xffb2b2b2);
////        }
//        
//        MyLog.d(TAG, "rating: " + comment.getRate());
//        
//        // comment content
//        TextView content = (TextView)itemView.findViewById(R.id.comment_content);
//        content.setText(comment.getContent());
//        MyLog.d(TAG, "name: " + comment.getContent());
//        
//        return itemView;
    }
    static class ViewHolder {
		TextView user;
		TextView time;
		TextView content;
	}
    public void setData(ArrayList<Comment> commentList) {
        mData = commentList;
        
        notifyDataSetChanged();
    }

}
