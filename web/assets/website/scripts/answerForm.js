const answerCommentLinks = document.querySelectorAll('.answer-comment');

answerCommentLinks.forEach((e) => e.addEventListener("click",async (e) => {

    const $this = e.currentTarget;

    const stateOpen = $this.dataset.open;

    const commentId = $this.dataset.commentId;
    const formCont = document.querySelector('.comment-answer--'+commentId);

    const usernameOther = $this.dataset.usernameOther;

    if(stateOpen == "0")
    {
        const form = await fetch("/answer-comment-form/"+commentId,{method: "GET"})
            .then((data) => data.text())
            .then((data) => data );
        
        
        formCont.innerHTML = form;
        
        $this.dataset.open = "1";
    }
    else
    {
        formCont.innerHTML = "";
        $this.dataset.open = "0";
    }

}))