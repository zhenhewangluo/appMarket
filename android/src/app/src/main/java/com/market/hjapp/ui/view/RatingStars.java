
package com.market.hjapp.ui.view;

import com.market.hjapp.R;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.ImageView;
import android.widget.LinearLayout;

public class RatingStars extends LinearLayout {

    private ImageView mStarOne;
    private ImageView mStarTwo;
    private ImageView mStarThree;
    private ImageView mStarFour;
    private ImageView mStarFive;
    private int mEmptyRes;
    private int mFillRes;
    
    public RatingStars(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    @Override
    protected void onFinishInflate() {
        super.onFinishInflate();
        
        mStarOne = (ImageView)findViewById(R.id.star1);
        mStarTwo = (ImageView)findViewById(R.id.star2);
        mStarThree = (ImageView)findViewById(R.id.star3);
        mStarFour = (ImageView)findViewById(R.id.star4);
        mStarFive = (ImageView)findViewById(R.id.star5);
    }

    public void setStarParam(int emptyRes, int fillRes) {
        mEmptyRes = emptyRes;
        mFillRes = fillRes;

//        LayoutParams firstStarParam = new LayoutParams(w, h);
//        mStarOne.setLayoutParams(firstStarParam);
//        
//        LayoutParams params = new LayoutParams(w, h);
//        params.leftMargin = gap;
//        
//        mStarTwo.setLayoutParams(params);
//        mStarThree.setLayoutParams(params);
//        mStarFour.setLayoutParams(params);
//        mStarFive.setLayoutParams(params);
    }
    
    public void setRating(int rating) {
        removePreviousRating();
        
        switch (rating) {
            case 5:
                fillStar(mStarFive);
            case 4:
                fillStar(mStarFour);
            case 3:
                fillStar(mStarThree);
            case 2:
                fillStar(mStarTwo);
            case 1:
                fillStar(mStarOne);
            case 0:
                break;
            default:
                throw new RuntimeException("Unknown rating: " + rating);
        }
    }

    private void removePreviousRating() {
        emptyStar(mStarOne);
        emptyStar(mStarTwo);
        emptyStar(mStarThree);
        emptyStar(mStarFour);
        emptyStar(mStarFive);
    }

    private void emptyStar(ImageView v) {
        v.setImageResource(mEmptyRes);
    }

    private void fillStar(ImageView v) {
        v.setImageResource(mFillRes);
    }

}
